// ** React Imports
import { forwardRef, Fragment, ReactElement, Ref } from 'react'

// ** MUI Imports
import IconButton from '@mui/material/IconButton'
import Grid from '@mui/material/Grid'
import Dialog from '@mui/material/Dialog'
import DialogContent from '@mui/material/DialogContent'
import Fade, { FadeProps } from '@mui/material/Fade'
import { Breakpoint } from '@mui/system';

// ** Icon Imports
import Icon from 'src/@core/components/icon'
import AddOrEditTableCore from './AddOrEditTableCore'

const Transition = forwardRef(function Transition(
  props: FadeProps & { children?: ReactElement<any, any> },
  ref: Ref<unknown>
) {
  return <Fade ref={ref} {...props} />
})

interface AddOrEditTableType {
  externalId: number
  id: string
  action: string
  addEditStructInfo: any
  open: boolean
  toggleAddTableDrawer: () => void
  addUserHandleFilter: (mobileEditPageIdEnableValue: boolean) => void
  backEndApi: string
  editViewCounter: number
  IsGetStructureFromEditDefault: number
  addEditViewShowInWindow: boolean
  CSRF_TOKEN: string
  dataGridLanguageCode: string
  dialogMaxWidth: Breakpoint
  toggleImagesPreviewListDrawer: (imagesPreviewList: string[], imagetype: string[]) => void
  handleIsLoadingTipChange: (status: boolean, showText: string) => void
  setForceUpdate: (value: any) => void
}

const AddOrEditTable = (props: AddOrEditTableType) => {
  // ** Props
  const { externalId, id, action, addEditStructInfo, open, toggleAddTableDrawer, addUserHandleFilter, backEndApi, editViewCounter, IsGetStructureFromEditDefault, addEditViewShowInWindow, CSRF_TOKEN, dataGridLanguageCode, dialogMaxWidth, toggleImagesPreviewListDrawer, handleIsLoadingTipChange, setForceUpdate } = props

  const handleClose = () => {
    toggleAddTableDrawer()
  }

  const addEditStructInfoNew = {...addEditStructInfo}
  if(addEditViewShowInWindow) {
    addEditStructInfoNew.canceltext = ""
  }

  return (
    <Fragment>
    {addEditViewShowInWindow ? 
      <Grid sx={{ pb: 8, px: { xs: 15, sm: 15 }, pt: { xs: 8, sm: 12.5 }, position: 'relative' }} style={{ height: `${addEditStructInfo.dialogContentHeight}`, width: '100%'}}>
        <AddOrEditTableCore externalId={externalId} id={id} action={action} addEditStructInfo={addEditStructInfoNew} open={open} toggleAddTableDrawer={toggleAddTableDrawer} addUserHandleFilter={addUserHandleFilter} backEndApi={backEndApi} editViewCounter={editViewCounter + 1} IsGetStructureFromEditDefault={IsGetStructureFromEditDefault} AddtionalParams={{"AddtionalParams":"AddtionalParams"}} CSRF_TOKEN={CSRF_TOKEN} dataGridLanguageCode={dataGridLanguageCode} toggleImagesPreviewListDrawer={toggleImagesPreviewListDrawer} handleIsLoadingTipChange={handleIsLoadingTipChange} setForceUpdate={setForceUpdate}/>
      </Grid>
      :
      <Dialog
        fullWidth
        open={open}
        maxWidth={dialogMaxWidth}
        scroll='body'
        onClose={handleClose}
        TransitionComponent={Transition}
      >
        <DialogContent sx={{ pb: 8, px: { xs: 8, sm: 15 }, pt: { xs: 8, sm: 12.5 }, position: 'relative' }} style={{ height: `${addEditStructInfo.dialogContentHeight}`, width: '100%' }}>
          <IconButton
            size='small'
            onClick={handleClose}
            sx={{ position: 'absolute', right: '1rem', top: '1rem' }}
          >
            <Icon icon='mdi:close' />
          </IconButton>
          <AddOrEditTableCore externalId={externalId} id={id} action={action} addEditStructInfo={addEditStructInfo} open={open} toggleAddTableDrawer={toggleAddTableDrawer} addUserHandleFilter={addUserHandleFilter} backEndApi={backEndApi} editViewCounter={editViewCounter + 1} IsGetStructureFromEditDefault={0} AddtionalParams={{"AddtionalParams":"AddtionalParams"}} CSRF_TOKEN={CSRF_TOKEN} dataGridLanguageCode={dataGridLanguageCode} toggleImagesPreviewListDrawer={toggleImagesPreviewListDrawer} handleIsLoadingTipChange={handleIsLoadingTipChange} setForceUpdate={setForceUpdate}/>
        </DialogContent>
      </Dialog>
    }
    </Fragment>
  )
}

export default AddOrEditTable
