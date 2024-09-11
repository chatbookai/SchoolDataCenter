// ** React Imports
import { forwardRef, ReactElement, Ref, Fragment, useState, useEffect, SetStateAction } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'
import Badge from '@mui/material/Badge'
import IconButton from '@mui/material/IconButton'
import Dialog from '@mui/material/Dialog'
import DialogContent from '@mui/material/DialogContent'
import Fade, { FadeProps } from '@mui/material/Fade'

//PDF
//import { pdfjs, Document, Page } from 'react-pdf';
//import 'react-pdf/dist/esm/Page/AnnotationLayer.css';
//import 'react-pdf/dist/esm/Page/TextLayer.css';

//EXCEL
import {OutTable, ExcelRenderer} from 'react-excel-renderer';

import dynamic from 'next/dynamic';

type FileViewerProps = {
  fileType: string
  filePath: string
  className: string
};

const FileViewer: React.ComponentType<FileViewerProps> = dynamic(
  () => import('react-file-viewer') as Promise<React.ComponentType<FileViewerProps>>,
  { ssr: false }
);



// Set up pdf.js worker
//pdfjs.GlobalWorkerOptions.workerSrc = `//cdnjs.cloudflare.com/ajax/libs/pdf.js/${pdfjs.version}/pdf.worker.js`;

// ** Icon Imports
import Icon from 'src/@core/components/icon'
import styles from './components/Excel2007.module.css';

const Transition = forwardRef(function Transition(
    props: FadeProps & { children?: ReactElement<any, any> },
    ref: Ref<unknown>
  ) {
    return <Fade ref={ref} {...props} />
  })

// ** Third Party Components
import clsx from 'clsx'
import { useKeenSlider } from 'keen-slider/react'

function ExcelViewer({ fileUrl }: { fileUrl: string; } ) {
  const [rows, setRows] = useState([]);
  const [cols, setCols] = useState([]);

  useEffect(() => {
    const fetchExcel = async () => {
      try {
        const response = await fetch(fileUrl);
        if (!response.ok) {
          throw new Error("Network response was not ok");
        }
        
        const blob = await response.blob();
        
        const reader = new FileReader();
        reader.onload = () => {
          ExcelRenderer(blob, (err: any, resp: { cols: SetStateAction<never[]>; rows: SetStateAction<never[]> }) => {
            if (err) {
              console.error(err);
            } else {
              console.log("resp.cols", resp.cols)
              const tempCols: SetStateAction<any[]> = []
              tempCols.push({name: '', key: 0})
              
              // @ts-ignore
              resp && resp.cols && resp.cols.map((Item: {'name': string, 'key': number}, Index: number)=>{
                if(Item.name) {
                  tempCols.push({name: Item.name, key: Index+1})
                }
              })
              
              // @ts-ignore
              setCols(tempCols);
              
              // @ts-ignore
              setRows(resp.rows);
            }
          });
        };
        
        reader.onerror = () => {
          throw new Error("Failed to read the blob data");
        };
        
        reader.readAsBinaryString(blob);
      } catch (error) {
        console.error("Error fetching or parsing the Excel file:", error);
      }
    };

    fetchExcel();
  }, [fileUrl]);

  return (
      <div>
        {rows && cols && (
          <OutTable
            data={rows}
            columns={cols}
            tableClassName={styles.ExcelTable2007}
            tableHeaderRowClass={styles.heading}
          />
        )}
      </div>
  );
}

interface ImagesPreviewType {
    open: boolean
    imagesList: string[]
    imagesType: string[]
    toggleImagesPreviewDrawer: () => void
  }

const ImagesPreview = (props: ImagesPreviewType) => {
  // ** Props
  const { open, imagesList, imagesType, toggleImagesPreviewDrawer } = props
  
  const handleClose = () => {
    toggleImagesPreviewDrawer()
  }

  //const [numPages, setNumPages] = useState<number>(0)    
  //function onDocumentLoadSuccess({ numPages }: { numPages: number; } ) {
  //    setNumPages(numPages);
  //}

  // ** States
  const [loaded, setLoaded] = useState<boolean>(false)
  const [currentSlide, setCurrentSlide] = useState<number>(0)

  // ** Hook
  const [sliderRef, instanceRef] = useKeenSlider<HTMLDivElement>({
    rtl: true,
    slideChanged(slider) {
      setCurrentSlide(slider.track.details.rel)
    },
    created() {
      setLoaded(true)
    }
  })

  return (
    <Dialog
        fullWidth
        open={open}
        maxWidth='md'
        scroll='body'
        onClose={handleClose}
        TransitionComponent={Transition}
      >
        <DialogContent sx={{ pb: 8, px: { xs: 8, sm: 8 }, pt: { xs: 8, sm: 12.5 }, position: 'relative' }}>
          <IconButton
            size='small'
            onClick={handleClose}
            sx={{ position: 'absolute', right: '0.3rem', top: '0.3rem' }}
          >
            <Icon icon='mdi:close' />
          </IconButton>
          <Fragment>
            <Box className='navigation-wrapper'>
                <Box ref={sliderRef} className='keen-slider'>
                {imagesList && imagesList.length>0 && imagesList.map((Url: string, UrlIndex: number)=>{
                  switch(imagesType[UrlIndex]) {
                    case 'image':
                      
                    return (
                          <Box className='keen-slider__slide' key={UrlIndex}>
                              <img src={Url} style={{'width':'100%', 'borderRadius': '4px'}}/>
                          </Box>
                      )
                    case 'pdf':
                      
                      return (
                          <Fragment key={UrlIndex}>
                          </Fragment>
                      );
                    case 'Word':

                      return (
                        <div style={{ width: '100%', color:'black'}} key={UrlIndex}>
                          <FileViewer fileType={'docx'} filePath={Url} className={styles.ExcelTable2007}/>
                        </div>
                      )
                    case 'Excel':
                      
                      return <ExcelViewer fileUrl={Url} />
                    default:
                      
                      return (
                          <Box className='keen-slider__slide' key={UrlIndex}>
                              <img src={Url} style={{'width':'100%', 'borderRadius': '4px'}}/>
                          </Box>
                      )
                  }                    
                })}
                </Box>
                {imagesList && imagesList[0]=="image" && loaded && instanceRef.current && (
                  <Fragment>
                      <Icon
                      icon='mdi:chevron-left'
                      className={clsx('arrow arrow-left', {
                          'arrow-disabled': currentSlide === 0
                      })}
                      onClick={(e: any) => { e.stopPropagation(); instanceRef.current && instanceRef.current.prev(); }}
                      />
                      <Icon
                      icon='mdi:chevron-right'
                      className={clsx('arrow arrow-right', {
                          'arrow-disabled': currentSlide === instanceRef.current.track.details.slides.length - 1
                      })}
                      onClick={(e: any) => { e.stopPropagation(); instanceRef.current && instanceRef.current.next(); }}
                      />
                  </Fragment>
                )}
            </Box>
            {imagesList && imagesList[0]=="image" && loaded && instanceRef.current && (
                <Box className='swiper-dots'>
                {[...Array(instanceRef.current.track.details.slides.length).keys()].map(idx => {
                    
                    return (
                      <Badge
                          key={idx}
                          variant='dot'
                          component='div'
                          className={clsx({
                          active: currentSlide === idx
                          })}
                          onClick={() => {
                            if (instanceRef.current) {
                              instanceRef.current.moveToIdx(idx);
                            }
                          }}
                          
                      ></Badge>
                    )
                })}
                </Box>
            )}
            </Fragment>
        </DialogContent>
      </Dialog >
    
  )
}

export default ImagesPreview
