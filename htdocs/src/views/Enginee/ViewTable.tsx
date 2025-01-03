// ** React Imports
import { forwardRef, ReactElement, Ref, Fragment } from 'react'

// ** MUI Imports
import IconButton from '@mui/material/IconButton'
import Dialog from '@mui/material/Dialog'
import DialogContent from '@mui/material/DialogContent'
import Fade, { FadeProps } from '@mui/material/Fade'

// ** Icon Imports
import Icon from 'src/@core/components/icon'
import Grid from '@mui/material/Grid'

import ViewTableCore from './ViewTableCore'
import { Breakpoint } from '@mui/system';

const Transition = forwardRef(function Transition(
  props: FadeProps & { children?: ReactElement<any, any> },
  ref: Ref<unknown>
) {
  return <Fade ref={ref} {...props} />
})

// ** Styles
import 'react-draft-wysiwyg/dist/react-draft-wysiwyg.css'

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
  addEditViewShowInWindow: boolean
  CSRF_TOKEN: string
  toggleImagesPreviewListDrawer: (imagesPreviewList: string[], imagetype: string[]) => void
  dialogMaxWidth: Breakpoint
  handleSetRightButtonIconOriginal?: any
  viewPageShareStatus?: boolean | undefined
  handSetViewPageShareStatus?: any
}

const ViewTable = (props: ViewTableType) => {
  // ** Props
  const { authConfig, externalId, id, action, pageJsonInfor, open, toggleViewTableDrawer, backEndApi, editViewCounter, addEditViewShowInWindow, CSRF_TOKEN, toggleImagesPreviewListDrawer, dialogMaxWidth, handleSetRightButtonIconOriginal, viewPageShareStatus, handSetViewPageShareStatus } = props

  const handleClose = () => {
    toggleViewTableDrawer()
  }

  return (
    <Fragment>
    {addEditViewShowInWindow ?
      <Grid sx={{ pb: 2, px: 0, pt: 1, position: 'relative' }} style={{ width: '100%' }}>
        <ViewTableCore authConfig={authConfig} externalId={Number(externalId)} id={id} action={action} pageJsonInfor={pageJsonInfor} open={open} toggleViewTableDrawer={toggleViewTableDrawer} backEndApi={backEndApi} editViewCounter={editViewCounter + 1} CSRF_TOKEN={CSRF_TOKEN} toggleImagesPreviewListDrawer={toggleImagesPreviewListDrawer} handleSetRightButtonIconOriginal={handleSetRightButtonIconOriginal} viewPageShareStatus={viewPageShareStatus} handSetViewPageShareStatus={handSetViewPageShareStatus} />
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
        <DialogContent sx={{ pb: 8, pl: { xs: 4, sm: 6 }, pr: { xs: 0, sm: 6 }, pt: { xs: 8, sm: 12.5 }, position: 'relative' }}>
          <IconButton
            size='small'
            onClick={handleClose}
            sx={{ position: 'absolute', right: '1rem', top: '1rem' }}
          >
            <Icon icon='mdi:close' />
          </IconButton>
          <ViewTableCore authConfig={authConfig} externalId={Number(externalId)} id={id} action={action} pageJsonInfor={pageJsonInfor} open={open} toggleViewTableDrawer={toggleViewTableDrawer} backEndApi={backEndApi} editViewCounter={editViewCounter + 1} CSRF_TOKEN={CSRF_TOKEN} toggleImagesPreviewListDrawer={toggleImagesPreviewListDrawer} handleSetRightButtonIconOriginal={handleSetRightButtonIconOriginal} viewPageShareStatus={viewPageShareStatus}/>
        </DialogContent>
      </Dialog >
    }
  </Fragment>
  )
}

export default ViewTable
