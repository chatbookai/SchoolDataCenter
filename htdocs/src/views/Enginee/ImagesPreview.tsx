// ** React Imports
import { forwardRef, ReactElement, Ref, Fragment, useState } from 'react'

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
//import {OutTable, ExcelRenderer} from 'react-excel-renderer';

// Set up pdf.js worker
//pdfjs.GlobalWorkerOptions.workerSrc = `//cdnjs.cloudflare.com/ajax/libs/pdf.js/${pdfjs.version}/pdf.worker.js`;

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const Transition = forwardRef(function Transition(
    props: FadeProps & { children?: ReactElement<any, any> },
    ref: Ref<unknown>
  ) {
    return <Fade ref={ref} {...props} />
  })

// ** Third Party Components
import clsx from 'clsx'
import { useKeenSlider } from 'keen-slider/react'

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
                        </div>
                      )
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
