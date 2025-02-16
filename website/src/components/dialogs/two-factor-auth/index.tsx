'use client'

// React Imports
import { useState } from 'react'
import type { ChangeEvent } from 'react'

// MUI Imports
import Dialog from '@mui/material/Dialog'
import DialogTitle from '@mui/material/DialogTitle'
import DialogActions from '@mui/material/DialogActions'
import DialogContent from '@mui/material/DialogContent'
import Grid from '@mui/material/Grid'
import Button from '@mui/material/Button'
import Typography from '@mui/material/Typography'
import IconButton from '@mui/material/IconButton'
import TextField from '@mui/material/TextField'
import Alert from '@mui/material/Alert'
import AlertTitle from '@mui/material/AlertTitle'

// Type Imports
import type { CustomInputHorizontalData } from '@core/components/custom-inputs/types'

// Component Imports
import CustomInputHorizontal from '@core/components/custom-inputs/Horizontal'
import DirectionalIcon from '@components/DirectionalIcon'

type TwoFactorAuthProps = {
  open: boolean
  setOpen: (open: boolean) => void
}

// Vars
const data: CustomInputHorizontalData[] = [
  {
    title: (
      <div className='flex items-center gap-1'>
        <i className='ri-settings-3-line text-textPrimary text-xl' />
        <Typography className='font-medium' color='text.primary'>
          Authenticator Apps
        </Typography>
      </div>
    ),
    value: 'app',
    isSelected: true,
    content: 'Get code from an app like Google Authenticator or Microsoft Authenticator.'
  },
  {
    title: (
      <div className='flex items-center gap-1'>
        <i className='ri-wechat-line text-textPrimary text-xl' />
        <Typography className='font-medium' color='text.primary'>
          SMS
        </Typography>
      </div>
    ),
    value: 'sms',
    content: 'We will send a code via SMS if you need to use your backup login method.'
  }
]

const SMSDialog = (handleAuthDialogClose: () => void) => {
  return (
    <>
      <DialogTitle variant='h5' className='flex flex-col items-start gap-2 sm:pbs-16 sm:pbe-6 sm:pli-16'>
        <div className='max-sm:is-[80%]'>Verify Your Mobile Number for SMS</div>
        <Typography component='span' className='flex flex-col'>
          Enter your mobile phone number with country code and we will send you a verification code.
        </Typography>
      </DialogTitle>
      <DialogContent className='overflow-visible pbs-0 sm:pbe-6 sm:pli-16'>
        <IconButton className='absolute block-start-4 inline-end-4' onClick={handleAuthDialogClose}>
          <i className='ri-close-line text-textSecondary' />
        </IconButton>

        <TextField fullWidth type='number' label='Mobile Number' placeholder='123 456 7890' />
      </DialogContent>
      <DialogActions className='pbs-0 sm:pbe-16 sm:pli-16'>
        <Button variant='outlined' type='reset' color='secondary' onClick={handleAuthDialogClose}>
          Cancel
        </Button>
        <Button
          color='success'
          variant='contained'
          type='submit'
          endIcon={<i className='ri-check-line' />}
          onClick={handleAuthDialogClose}
        >
          Submit
        </Button>
      </DialogActions>
    </>
  )
}

const AppDialog = (handleAuthDialogClose: () => void) => {
  return (
    <>
      <DialogTitle variant='h4' className='flex justify-center sm:pbs-16 sm:pbe-6 sm:pli-16'>
        <div className='max-sm:is-[80%] max-sm:text-center'>Add Authenticator App</div>
      </DialogTitle>
      <DialogContent className='flex flex-col gap-6 pbs-0 sm:pbe-6 sm:pli-16'>
        <IconButton className='absolute block-start-4 inline-end-4' onClick={handleAuthDialogClose}>
          <i className='ri-close-line text-textSecondary' />
        </IconButton>
        <div className='flex flex-col gap-2'>
          <Typography variant='h5'>Authenticator Apps</Typography>
          <Typography>
            Using an authenticator app like Google Authenticator, Microsoft Authenticator, Authy, or 1Password, scan the
            QR code. It will generate a 6 digit code for you to enter below.
          </Typography>
        </div>
        <div className='flex justify-center'>
          <img alt='qr-code' src='/images/misc/barcode.png' width={150} />
        </div>
        <div className='flex flex-col gap-4'>
          <Alert severity='warning' icon={false}>
            <AlertTitle>ASDLKNASDA9AHS678dGhASD78AB</AlertTitle>
            If you having trouble using the QR code, select manual entry on your app
          </Alert>
          <TextField fullWidth label='Enter Authentication Code' placeholder='Enter Authentication Code' />
        </div>
      </DialogContent>
      <DialogActions className='pbs-0 sm:pbe-16 sm:pli-16'>
        <Button variant='outlined' type='reset' color='secondary' onClick={handleAuthDialogClose}>
          Cancel
        </Button>
        <Button
          color='success'
          variant='contained'
          type='submit'
          endIcon={<i className='ri-check-line' />}
          onClick={handleAuthDialogClose}
        >
          Submit
        </Button>
      </DialogActions>
    </>
  )
}

const TwoFactorAuth = ({ open, setOpen }: TwoFactorAuthProps) => {
  // Vars
  const initialSelectedOption: string = data.filter(item => item.isSelected)[
    data.filter(item => item.isSelected).length - 1
  ].value

  // States
  const [authType, setAuthType] = useState<string>(initialSelectedOption)
  const [showAuthDialog, setShowAuthDialog] = useState<boolean>(false)

  const handleClose = () => {
    setOpen(false)

    if (authType !== 'app') {
      setAuthType('app')
    }
  }

  const handleAuthDialogClose = () => {
    setShowAuthDialog(false)
    setShowAuthDialog(false)

    if (authType !== 'app') {
      setTimeout(() => {
        setAuthType('app')
      }, 250)
    }
  }

  const handleOptionChange = (prop: string | ChangeEvent<HTMLInputElement>) => {
    if (typeof prop === 'string') {
      setAuthType(prop)
    } else {
      setAuthType((prop.target as HTMLInputElement).value)
    }
  }

  return (
    <>
      <Dialog fullWidth maxWidth='md' scroll='body' open={open} onClose={() => setOpen(false)}>
        <DialogTitle variant='h4' className='flex gap-2 flex-col items-center sm:pbs-16 sm:pbe-6 sm:pli-16'>
          <div className='max-sm:is-[80%] max-sm:text-center'>Select Authentication Method</div>
          <Typography component='span' className='flex flex-col text-center'>
            You also need to select a method by which the proxy authenticates to the directory serve.
          </Typography>
        </DialogTitle>
        <DialogContent className='pbs-0 sm:pli-16'>
          <IconButton onClick={handleClose} className='absolute block-start-4 inline-end-4'>
            <i className='ri-close-line text-textSecondary' />
          </IconButton>
          <Grid container spacing={6}>
            {data.map((item, index) => (
              <CustomInputHorizontal
                key={index}
                type='radio'
                selected={authType}
                handleChange={handleOptionChange}
                data={item}
                gridProps={{ xs: 12 }}
                name='auth-method'
              />
            ))}
          </Grid>
        </DialogContent>
        <DialogActions className='pbs-0 sm:pbe-16 sm:pli-16'>
          <Button
            variant='contained'
            endIcon={<DirectionalIcon ltrIconClass='ri-arrow-right-line' rtlIconClass='ri-arrow-left-line' />}
            onClick={() => {
              setOpen(false)
              setShowAuthDialog(true)
            }}
            className='capitalize'
          >
            Continue
          </Button>
        </DialogActions>
      </Dialog>

      <Dialog fullWidth maxWidth='md' scroll='body' open={showAuthDialog} onClose={handleAuthDialogClose}>
        <form onSubmit={e => e.preventDefault()}>
          {authType === 'sms' ? SMSDialog(handleAuthDialogClose) : AppDialog(handleAuthDialogClose)}
        </form>
      </Dialog>
    </>
  )
}

export default TwoFactorAuth
