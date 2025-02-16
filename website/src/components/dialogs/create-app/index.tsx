'use client'

// React Imports
import { useState } from 'react'

// MUI Imports
import Dialog from '@mui/material/Dialog'
import DialogTitle from '@mui/material/DialogTitle'
import DialogContent from '@mui/material/DialogContent'
import IconButton from '@mui/material/IconButton'
import Typography from '@mui/material/Typography'
import Step from '@mui/material/Step'
import StepLabel from '@mui/material/StepLabel'
import Stepper from '@mui/material/Stepper'
import Avatar from '@mui/material/Avatar'

// Third-party Imports
import classnames from 'classnames'

// Component Imports
import Details from './Details'
import FrameWork from './FrameWork'
import Database from './Database'
import Billing from './Billing'
import Submit from './Submit'

// Styled Component Imports
import StepperWrapper from '@core/styles/stepper'

type CreateAppProps = {
  open: boolean
  setOpen: (open: boolean) => void
}

type stepperProps = {
  icon: string
  title: string
  subtitle: string
  active?: boolean
}

const steps: stepperProps[] = [
  {
    icon: 'ri-file-text-line',
    title: 'Details',
    subtitle: 'Enter Details'
  },
  {
    icon: 'ri-star-smile-line',
    title: 'FrameWorks',
    subtitle: 'Select Framework',
    active: true
  },
  {
    icon: 'ri-pie-chart-2-line',
    title: 'Database',
    subtitle: 'Select Database'
  },
  {
    icon: 'ri-bank-card-line',
    title: 'Billing',
    subtitle: 'Payment Details'
  },
  {
    icon: 'ri-check-double-line',
    title: 'Submit',
    subtitle: 'Submit'
  }
]

const renderStepCount = (activeStep: number, isLastStep: boolean, handleNext: () => void, handlePrev: () => void) => {
  const Tag =
    activeStep === 0
      ? Details
      : activeStep === 1
        ? FrameWork
        : activeStep === 2
          ? Database
          : activeStep === 3
            ? Billing
            : Submit

  return <Tag activeStep={activeStep} handleNext={handleNext} handlePrev={handlePrev} isLastStep={isLastStep} />
}

const CreateApp = ({ open, setOpen }: CreateAppProps) => {
  // States
  const [activeStep, setActiveStep] = useState(0)

  // Vars
  const isLastStep = activeStep === steps.length - 1

  const handleClose = () => {
    setOpen(false)
    setActiveStep(0)
  }

  const handleStep = (step: number) => () => {
    setActiveStep(step)
  }

  const handleNext = () => {
    if (!isLastStep) {
      setActiveStep(prevActiveStep => prevActiveStep + 1)
    } else {
      handleClose()
    }
  }

  const handlePrev = () => {
    setActiveStep(prevActiveStep => prevActiveStep - 1)
  }

  return (
    <Dialog fullWidth maxWidth='md' open={open} onClose={handleClose} scroll='body'>
      <DialogTitle variant='h4' className='flex gap-2 flex-col text-center sm:pbs-16 sm:pbe-6 sm:pli-16 '>
        Create App
        <Typography component='span' className='flex flex-col text-center'>
          Provide data with this form to create your app.
        </Typography>
      </DialogTitle>
      <DialogContent className='pbs-0 sm:pli-16 sm:pbe-16'>
        <IconButton onClick={handleClose} className='absolute block-start-4 inline-end-4'>
          <i className='ri-close-line text-textSecondary' />
        </IconButton>
        <div className='flex gap-y-6 pbs-1 flex-col md:flex-row'>
          <StepperWrapper>
            <Stepper
              nonLinear
              activeStep={activeStep}
              orientation='vertical'
              connector={<></>}
              className='flex flex-col gap-6 min-is-[220px]'
            >
              {steps.map((label, index) => {
                return (
                  <Step key={index} onClick={handleStep(index)}>
                    <StepLabel icon={<></>} className='p-0 cursor-pointer'>
                      <div className='step-label gap-4'>
                        <Avatar
                          variant='rounded'
                          className={classnames(
                            { 'bg-primary text-white shadow-xs': activeStep === index },
                            { 'bg-primaryLight text-primary': activeStep > index }
                          )}
                        >
                          <i className={label.icon as string} />
                        </Avatar>
                        <div className='flex flex-col gap-1'>
                          <Typography className='uppercase font-medium' color='text.primary'>
                            {label.title}
                          </Typography>
                          <Typography variant='body2'>{label.subtitle}</Typography>
                        </div>
                      </div>
                    </StepLabel>
                  </Step>
                )
              })}
            </Stepper>
          </StepperWrapper>
          <div className='flex-1'>{renderStepCount(activeStep, isLastStep, handleNext, handlePrev)}</div>
        </div>
      </DialogContent>
    </Dialog>
  )
}

export default CreateApp
