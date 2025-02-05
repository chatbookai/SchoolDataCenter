// ** React Imports
import { useState, useEffect, Fragment } from 'react'

// ** MUI Imports
import Typography from '@mui/material/Typography'
import Box from '@mui/material/Box'
import Table from '@mui/material/Table'
import TableRow from '@mui/material/TableRow'
import TableBody from '@mui/material/TableBody'
import TableHead from '@mui/material/TableHead'
import { styled } from '@mui/material/styles'
import TableCell, { TableCellBaseProps } from '@mui/material/TableCell'
import Grid from '@mui/material/Grid'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import ListItem from '@mui/material/ListItem'
import Button from '@mui/material/Button'
import CircularProgress from '@mui/material/CircularProgress'

// ** Icon Imports
import Icon from 'src/@core/components/icon'
import {isMobile} from 'src/configs/functions'

// ** Config
import { defaultConfig } from 'src/configs/auth'
import axios from 'axios'
import Mousetrap from 'mousetrap';

// ** Store Imports
import { useSelector } from 'react-redux'

// ** Styles
import 'react-draft-wysiwyg/dist/react-draft-wysiwyg.css'

import { RootState } from 'src/store/index'
import { Divider } from '@mui/material'
import { DecryptDataAES256GCM } from 'src/configs/functions'

import ModelMiddleSchoolSoulAssessment from 'src/views/Enginee/ModelMiddleSchoolSoulAssessment'

// ** Next Imports
import Link from 'next/link'

const MUITableCell = styled(TableCell)<TableCellBaseProps>(({ theme }) => ({
  borderBottom: 0,
  paddingLeft: '0 !important',
  paddingRight: '0 !important',
  paddingTop: `${theme.spacing(1)} !important`,
  paddingBottom: `${theme.spacing(1)} !important`
}))

interface ViewTableType {
  id: string
  action: string
  open: boolean
  toggleViewTableDrawer: () => void
  backEndApi: string
  editViewCounter: number
  authConfig: any
  externalId: number
  pageJsonInfor: {}
  CSRF_TOKEN: string
  toggleImagesPreviewListDrawer: (imagesPreviewList: string[], imagetype: string[]) => void
  handleSetRightButtonIconOriginal?: any
  viewPageShareStatus?: boolean
  handSetViewPageShareStatus?: any
}

const ImgStyled = styled('img')(({ theme }) => ({
  width: 120,
  borderRadius: 4,
  marginRight: theme.spacing(5)
}))

const ImgStyled68 = styled('img')(({ theme }) => ({
  width: 65,
  borderRadius: 4,
  marginRight: theme.spacing(1)
}))

const CustomLink = styled(Link)({
  textDecoration: "none",
  color: "inherit",
});

const ViewTableCore = (props: ViewTableType) => {
  // ** Props
  const { authConfig, externalId, id, action, toggleViewTableDrawer, backEndApi, editViewCounter, CSRF_TOKEN, toggleImagesPreviewListDrawer, handleSetRightButtonIconOriginal, viewPageShareStatus, handSetViewPageShareStatus } = props
  console.log("externalId props", externalId)

  const isMobileData = isMobile()
  console.log("isMobileData", isMobileData)

  // ** Hooks
  //const dispatch = useDispatch<AppDispatch>()
  const [isLoading, setIsLoading] = useState(false);
  const store = useSelector((state: RootState) => state.user)
  const titletext: string = store.view_default.titletext;
  const [defaultValuesView, setDefaultValuesView] = useState<{[key:string]:any}>({})
  const [childTable, setChildTable] = useState<{[key:string]:any}>({})

  const addFilesOrDatesDefault:{[key:string]:any}[][] = []
  const [newTableRowData, setNewTableRowData] = useState(addFilesOrDatesDefault)
  const [approvalNodes, setApprovalNodes] = useState<{[key:string]:any}>({})
  const [print, setPrint] = useState<{[key:string]:any}>({})
  const [model, setModel] = useState<string>("")

  useEffect(() => {
    Mousetrap.bind(['alt+c', 'command+c'], handleClose);

    return () => {
      Mousetrap.unbind(['alt+c', 'command+c']);
    }
  });

  //console.log("newTableRowData--------------------------------", newTableRowData)

  const storedToken = window.localStorage.getItem(defaultConfig.storageTokenKeyName)!
  const AccessKey = window.localStorage.getItem(defaultConfig.storageAccessKeyName)!

  useEffect(() => {
    if (action == "view_default" && editViewCounter > 0) {
      setIsLoading(true)
      axios
        .get(authConfig.backEndApiHost + backEndApi, { headers: { Authorization: storedToken+"::::"+CSRF_TOKEN }, params: { action, id, editViewCounter, isMobileData } })
        .then(res => {
          let dataJson: any = null
          const data = res.data
          if(data && data.isEncrypted == "1" && data.data)  {
              const i = data.data.slice(0, 32);
              const t = data.data.slice(-32);
              const e = data.data.slice(32, -32);
              const k = AccessKey;
              const DecryptDataAES256GCMData = DecryptDataAES256GCM(e, i, t, k)
              try {
                  dataJson = JSON.parse(DecryptDataAES256GCMData)
              }
              catch(Error: any) {
                  console.log("DecryptDataAES256GCMData view_default Error", Error)

                  dataJson = data
              }
          }
          else {

              dataJson = data
          }
          if (dataJson.status == "OK") {
            setDefaultValuesView(dataJson.data)
            if(dataJson.childtable) {
              setChildTable(dataJson.childtable)
            }
            if(dataJson.newTableRowData) {
              setNewTableRowData(dataJson.newTableRowData)
            }
            if(dataJson.ApprovalNodes) {
              setApprovalNodes(dataJson.ApprovalNodes)
            }
            if(dataJson.print && isMobileData==false) {
              setPrint(dataJson.print)
            }
          }
          if(data && data.model) {
            setModel(data.model)
            if(data.model == "测评模式")  {
              handleSetRightButtonIconOriginal('material-symbols:ios-share')
            }
            else {
              handleSetRightButtonIconOriginal('')
            }
          }
          setIsLoading(false)
        })
        .catch(() => {
          setIsLoading(false)
          console.log("axios.get editUrl return")
        })
    }
  }, [id, editViewCounter, isMobileData])

  //Need refresh data every time.

  const handleClose = () => {
    toggleViewTableDrawer()
  }

  return (
    <Fragment>
      {isLoading == false && model && model == "测评模式" && (
        <Fragment>
          <ModelMiddleSchoolSoulAssessment authConfig={authConfig} modelOriginal={model} dataOriginal={defaultValuesView} id={id} backEndApi={backEndApi} viewPageShareStatus={viewPageShareStatus} handSetViewPageShareStatus={handSetViewPageShareStatus}/>
        </Fragment>
      )}
      {isLoading == false && model == "" && (
        <Fragment>
          {isMobileData == false && (
            <Box sx={{ mb: 8, textAlign: 'center' }}>
              <Typography variant='h5' sx={{ mb: 3 }}>
                {titletext}
              </Typography>
              <Typography variant='body2'>{store.view_default.titlememo ? store.view_default.titlememo : ''}</Typography>
            </Box>
          )}
          <Card key={"AllFieldsMode"} sx={{mt: 0}}>
            <CardContent sx={{ px: { xs: 9, sm: 12 }, mt: 0 }}>
              <Grid container spacing={6} sx={{pt: '10px'}}>
                <Table>
                  <TableBody>
                    {newTableRowData && newTableRowData.length>0 && newTableRowData.map((RowData: any, RowData_index: number) => {

                      const colSpan = RowData.length == 2 ? 1 : 3 ;

                      return (
                        <TableRow key={RowData_index}>
                          {RowData && RowData.map((CellData: any, FieldArray_index: number) => {
                            const FieldArray = CellData.FieldArray
                            if(FieldArray == null) return

                            //开始根据表单中每个字段的类型,进行不同的渲染,此部分比较复杂,注意代码改动.
                            if (FieldArray.type == "input"
                              || FieldArray.type == "email"
                              || FieldArray.type == "number"
                              || FieldArray.type == "date"
                              || FieldArray.type == "month"
                              || FieldArray.type == "time"
                              || FieldArray.type == "datetime"
                              || FieldArray.type == "slider"
                              || FieldArray.type == "readonly"
                              || FieldArray.type == "autoincrement"
                              || FieldArray.type == "autoincrementdate"
                            ) {
                              if (CellData.Value == "1971-01-01" || CellData.Value == "1971-01-01 00:00:00" || CellData.Value == "1971-01") {
                                CellData.Value = "";
                              }

                              return (
                                <Fragment key={FieldArray_index}>
                                  <MUITableCell sx={{ maxWidth: '15%', whiteSpace: 'nowrap' }}>
                                    <Typography style={{ fontWeight: 'bold' }} variant='body2'>{FieldArray.label}:</Typography>
                                  </MUITableCell>
                                  <MUITableCell sx={{ minWidth: '35%' }} colSpan={colSpan}>{CellData.Value}</MUITableCell>
                                </Fragment>
                              )
                            }//end if
                            else if (FieldArray.type == "password" || FieldArray.type == "EncryptField" || FieldArray.type == "comfirmpassword") {
                              // Nothing to do
                              return (
                                <Fragment key={FieldArray_index}>
                                  <MUITableCell sx={{ width: '15%', whiteSpace: 'nowrap' }}>
                                    <Typography style={{ fontWeight: 'bold' }} variant='body2'>{FieldArray.label}:</Typography>
                                  </MUITableCell>
                                  <MUITableCell sx={{ width: '35%', whiteSpace: 'wrap', wordWrap: 'break-word' }}>******</MUITableCell>
                                </Fragment>
                              )
                            }
                            else if (FieldArray.type == "select" || FieldArray.type == "autocomplete" || FieldArray.type == "radiogroup") {

                              return (
                                <Fragment key={FieldArray_index}>
                                  <MUITableCell sx={{ width: '15%', whiteSpace: 'nowrap' }}>
                                    <Typography style={{ fontWeight: 'bold' }} variant='body2'>{FieldArray.label}:</Typography>
                                  </MUITableCell>
                                  <MUITableCell sx={{ width: '35%' }} colSpan={colSpan}>{CellData.Value}</MUITableCell>
                                </Fragment>
                              )
                            }
                            else if (FieldArray.type == "autocompletemulti") {

                              return (
                                <Fragment key={FieldArray_index}>
                                  <MUITableCell sx={{ width: '15%', whiteSpace: 'nowrap' }}>
                                    <Typography style={{ fontWeight: 'bold' }} variant='body2'>{FieldArray.label}:</Typography>
                                  </MUITableCell>
                                  <MUITableCell sx={{ width: '35%' }} colSpan={colSpan}>{CellData.Value}</MUITableCell>
                                </Fragment>
                              )
                            }
                            else if (FieldArray.type == "checkbox") {

                              return (
                                <Fragment key={FieldArray_index}>
                                  <MUITableCell sx={{ width: '15%', whiteSpace: 'nowrap' }}>
                                    <Typography style={{ fontWeight: 'bold' }} variant='body2'>{FieldArray.label}:</Typography>
                                  </MUITableCell>
                                  <MUITableCell sx={{ width: '35%' }} colSpan={colSpan}>{CellData.Value}</MUITableCell>
                                </Fragment>
                              )
                            }
                            else if (FieldArray.type == "textarea") {

                              console.log("CellData.Value", CellData.Value)

                              return (
                                <Fragment key={FieldArray_index}>
                                    <MUITableCell sx={{ width: '50%' }} colSpan={Number(colSpan) + 1}>
                                      <Typography style={{ fontWeight: 'bold' }} variant='body2'>{FieldArray.label}</Typography>
                                      <Typography style={{ whiteSpace: 'pre-wrap', paddingLeft: 14 }} variant='body2'>{CellData.Value}</Typography>
                                    </MUITableCell>
                                </Fragment>
                              )
                            }
                            else if (FieldArray.type == "code") {

                              return (
                                <Fragment key={FieldArray_index}>
                                    <MUITableCell sx={{ width: '50%', whiteSpace: 'pre-line' }} colSpan={Number(colSpan) + 1}>
                                      {FieldArray.label}:
                                      <div dangerouslySetInnerHTML={{ __html: CellData.Value }} />
                                    </MUITableCell>
                                </Fragment>
                              )
                            }
                            else if (FieldArray.type == "avatar" && CellData.Value != undefined) {

                              return (
                                <Fragment key={FieldArray_index}>
                                  <MUITableCell sx={{ width: '15%', whiteSpace: 'nowrap' }}>
                                    <Typography style={{ fontWeight: 'bold' }} variant='body2'>{FieldArray.label}:</Typography>
                                  </MUITableCell>
                                  <MUITableCell sx={{ width: '35%' }} colSpan={colSpan}>
                                  <Box sx={{ display: 'flex', alignItems: 'center',cursor: 'pointer',':hover': {cursor: 'pointer',}, }} onClick={() => toggleImagesPreviewListDrawer([authConfig.backEndApiHost+CellData.Value], ['image'])}>
                                    <ImgStyled src={authConfig.backEndApiHost+CellData.Value} alt={FieldArray.helptext} />
                                  </Box>
                                  </MUITableCell>
                                </Fragment>
                              )
                            }
                            else if ((FieldArray.type == "images" || FieldArray.type == "images2") && CellData.Value != undefined) {

                              return (
                                <Fragment key={FieldArray_index}>
                                  <MUITableCell sx={{ width: '50%' }} colSpan={colSpan+1}>
                                    {FieldArray.label}:
                                    <div style={{ display: 'flex', flexWrap: 'wrap' }}>
                                    {CellData.Value && CellData.Value.length>0 && CellData.Value.map((FileUrl: any, index: number)=>{
                                      return (
                                        <div key={index} style={{ flex: '0 0 auto'}}>
                                          <ListItem style={{ padding: '2px' }}>
                                              <div className='file-details' style={{ display: 'flex', overflow: 'hidden' }}>
                                              <Box sx={{ display: 'flex', alignItems: 'center', cursor: 'pointer', ':hover': { cursor: 'pointer' } }} onClick={() => toggleImagesPreviewListDrawer([authConfig.backEndApiHost + FileUrl['webkitRelativePath']], ['image'])}>
                                                  <ImgStyled68 src={authConfig.backEndApiHost + FileUrl['webkitRelativePath']} />
                                              </Box>
                                              </div>
                                          </ListItem>
                                        </div>
                                      )
                                    })}
                                    </div>
                                  </MUITableCell>
                                </Fragment>
                              )
                            }
                            else if ((FieldArray.type == "files" || FieldArray.type == "files2") && CellData.Value != undefined) {

                              return (
                                <Fragment key={FieldArray_index}>
                                  <MUITableCell sx={{ width: '50%' }} colSpan={colSpan+1}>
                                    {FieldArray.label}:
                                    <div style={{ display: 'flex', flexWrap: 'wrap' }}>
                                    {CellData.Value && CellData.Value.length>0 && CellData.Value.map((FileUrl: any)=>{

                                      return (
                                        <ListItem key={FileUrl['name']} style={{padding: "3px"}}>
                                        <div className='file-details' style={{display: "flex"}}>
                                          <div style={{padding: "3px 3px 0 0"}}>
                                            {FileUrl.type.startsWith('image') ?
                                            <Box sx={{ display: 'flex', alignItems: 'center',cursor: 'pointer',':hover': {cursor: 'pointer',}, }} onClick={() => toggleImagesPreviewListDrawer([authConfig.backEndApiHost+FileUrl['webkitRelativePath']], ['image'])}>
                                              <ImgStyled68 src={authConfig.backEndApiHost+FileUrl['webkitRelativePath']} />
                                            </Box>
                                            : <Icon icon='mdi:file-document-outline' fontSize={28}/>
                                            }
                                          </div>
                                          <div>
                                            {FileUrl['type']=="pdf" || FileUrl['type']=="Excel" || FileUrl['type']=="Word" || FileUrl['type']=="PowerPoint" ?
                                              <Typography className='file-name'><CustomLink href={authConfig.backEndApiHost+FileUrl['webkitRelativePath']} download={FileUrl['name']}>{FileUrl['name']}</CustomLink></Typography>
                                            :
                                              ''
                                            }
                                            {FileUrl['type']=="file" ?
                                              <Typography className='file-name'><CustomLink href={authConfig.backEndApiHost+FileUrl['webkitRelativePath']} download={FileUrl['name']}>{FileUrl['name']}</CustomLink></Typography>
                                            :
                                              ''
                                            }
                                            {FileUrl['size']>0 ?
                                              <Typography className='file-size' variant='body2'>
                                                  {Math.round(FileUrl['size'] / 100) / 10 > 1000
                                                  ? `${(Math.round(FileUrl['size'] / 100) / 10000).toFixed(1)} mb`
                                                  : `${(Math.round(FileUrl['size'] / 100) / 10).toFixed(1)} kb`}
                                              </Typography>
                                              : ''
                                            }
                                          </div>
                                        </div>
                                        </ListItem>
                                        )
                                    })}
                                    </div>
                                  </MUITableCell>
                                </Fragment>
                              )
                            }
                            else if (FieldArray.type == "editor") {

                              return (
                                  <Fragment key={FieldArray_index}>
                                    <MUITableCell sx={{ width: '50%' }} colSpan={Number(colSpan) + 1}>
                                      {FieldArray.label}
                                      <div dangerouslySetInnerHTML={{ __html: CellData.Value }} />
                                    </MUITableCell>
                                  </Fragment>
                              )
                            }
                            else if (FieldArray.type == "tablefiltercolor" ||
                                    FieldArray.type == "tablefilter" ||
                                    FieldArray.type == "radiogroup" ||
                                    FieldArray.type == "radiogroupcolor"
                                    ) {

                              return (
                                <Fragment key={FieldArray_index}>
                                  <MUITableCell sx={{ width: '15%', whiteSpace: 'nowrap' }}>
                                    <Typography style={{ fontWeight: 'bold' }} variant='body2'>{FieldArray.label}:</Typography>
                                  </MUITableCell>
                                  <MUITableCell sx={{ width: '35%' }} colSpan={colSpan}>{CellData.Value}</MUITableCell>
                                </Fragment>
                              )
                            }
                            else {

                              return (
                                <Fragment key={FieldArray_index}>
                                  <MUITableCell sx={{ maxWidth: '15%', whiteSpace: 'nowrap' }}>
                                    <Typography style={{ fontWeight: 'bold' }} variant='body2'>{FieldArray.label}:</Typography>
                                  </MUITableCell>
                                  <MUITableCell sx={{ minWidth: '35%' }} colSpan={colSpan}>
                                    {CellData && CellData.Value && typeof CellData.Value === 'string' ? CellData.Value : FieldArray.type + " " + (CellData && CellData.Value ? CellData.Value.toString() : '')}
                                  </MUITableCell>
                                </Fragment>
                              )
                            }

                          })}
                        </TableRow>
                      )

                    })}

                  </TableBody>
                </Table>

                {approvalNodes && approvalNodes.Nodes && approvalNodes.Nodes.length>0 && approvalNodes.Fields ?
                  <Fragment>
                  <Divider />
                    <Table>
                      <TableHead>
                        <TableRow key="ChildTableTableRow">
                          {approvalNodes.Fields && approvalNodes.Fields.map((Item: any, ItemIndex: number) => {

                            return <MUITableCell sx={{ width: '20%', whiteSpace: 'nowrap' }} key={ItemIndex}>{Item}</MUITableCell>
                          })}
                        </TableRow>
                      </TableHead>
                      <TableBody>
                        {approvalNodes.Nodes && approvalNodes.Nodes.map((Node: string, NodeIndex: number) => {
                          const FieldTemp = `${Node}${approvalNodes.Fields[1]}`

                          return (
                            <Fragment key={NodeIndex}>
                              {FieldTemp in defaultValuesView ?
                                <TableRow>
                                  {approvalNodes.Fields && approvalNodes.Fields.map((Item: any, ItemIndex: number) => {
                                    const FieldTemp = `${Node}${Item}`

                                    return <MUITableCell key={ItemIndex}>{Item=="审核结点" ? Node : defaultValuesView[FieldTemp]}</MUITableCell>
                                  })}
                                </TableRow>
                                : '' }
                            </Fragment>
                          )
                        })}
                      </TableBody>
                    </Table>
                  </Fragment>
                  : ''
                }

                {childTable && childTable.allFields && childTable.data ?
                  <Fragment>
                  <Divider />
                    <Table>
                      <TableHead>
                        <TableRow key="ChildTableTableRow">
                          {childTable.allFields && childTable.allFields.Default.map((Item: any, Index: number) => {

                            return <MUITableCell key={Index}>{Item.code? Item.code : Item.name}</MUITableCell>
                          })}
                        </TableRow>
                      </TableHead>
                      <TableBody>
                        {childTable.data && childTable.data.map((RowItem: any, RowIndex: number) => {

                          return (
                            <TableRow key={RowIndex}>
                              {childTable.allFields && childTable.allFields.Default.map((Item: any, Index: number) => {

                                return <MUITableCell key={Index}>{Item.type=="autocomplete" ? RowItem[Item.code? Item.code : Item.name] : RowItem[Item.name]}</MUITableCell>
                              })}
                            </TableRow>
                          )
                        })}
                      </TableBody>
                    </Table>
                  </Fragment>
                  : ''
                }

                {print && print.text ?
                  <Grid container justifyContent="flex-end">
                      <Button onClick={()=>{window.print();}}  variant='contained' size="small">{print.text}</Button>
                  </Grid>
                  : ''
                }

              </Grid>
            </CardContent>
          </Card>
        </Fragment>
      )}
      {isLoading == true && (
        <Grid item xs={12} sm={12} container justifyContent="space-around">
          <Box sx={{ mt: 6, mb: 6, display: 'flex', alignItems: 'center', flexDirection: 'column' }}>
              <CircularProgress />
              <Typography sx={{pt:5, pb:5}}>正在加载中</Typography>
          </Box>
        </Grid>
      )}
    </Fragment>
  )
}

export default ViewTableCore
