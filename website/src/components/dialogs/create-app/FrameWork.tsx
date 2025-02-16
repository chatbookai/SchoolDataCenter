// React Imports
import { useState } from 'react'
import type { ChangeEvent } from 'react'

// MUI Imports
import Typography from '@mui/material/Typography'
import Radio from '@mui/material/Radio'
import Button from '@mui/material/Button'

// Component Imports
import DirectionalIcon from '@components/DirectionalIcon'

// Component Imports
import CustomAvatar from '@core/components/mui/Avatar'

type Props = {
  activeStep: number
  isLastStep: boolean
  handleNext: () => void
  handlePrev: () => void
}

const FrameWork = ({ activeStep, isLastStep, handleNext, handlePrev }: Props) => {
  // States
  const [value, setValue] = useState<string>('react')

  const handleChange = (event: ChangeEvent<HTMLInputElement>) => {
    setValue(event.target.value)
  }

  return (
    <div className='flex flex-col gap-6'>
      <div className='flex flex-col gap-4'>
        <Typography variant='h5'>Select Framework</Typography>
        <div onClick={() => setValue('react')} className='flex items-center justify-between cursor-pointer gap-4'>
          <div className='flex items-center gap-4'>
            <CustomAvatar variant='rounded' skin='light' color='info' size={46}>
              <img src='/images/logos/react.png' alt='react' height={30} width={30} />
            </CustomAvatar>
            <div className='flex flex-col gap-1'>
              <Typography className='font-medium' color='text.primary'>
                React Native
              </Typography>
              <Typography variant='body2'>Create truly native apps</Typography>
            </div>
          </div>
          <Radio value='react' onChange={handleChange} checked={value === 'react'} />
        </div>

        <div onClick={() => setValue('angular')} className='flex items-center justify-between cursor-pointer gap-4'>
          <div className='flex items-center gap-4'>
            <CustomAvatar variant='rounded' skin='light' color='error' size={46}>
              <img src='/images/logos/angular.png' alt='angular' height={30} width={30} />
            </CustomAvatar>
            <div className='flex flex-col gap-1'>
              <Typography className='font-medium' color='text.primary'>
                Angular
              </Typography>
              <Typography variant='body2'>Most suited for your application</Typography>
            </div>
          </div>
          <Radio value='angular' onChange={handleChange} checked={value === 'angular'} />
        </div>
        <div onClick={() => setValue('vuejs')} className='flex items-center justify-between cursor-pointer gap-4'>
          <div className='flex items-center gap-4'>
            <CustomAvatar variant='rounded' skin='light' color='success' size={46}>
              <img src='/images/logos/vue.png' alt='vue' height={30} width={30} />
            </CustomAvatar>
            <div className='flex flex-col gap-1'>
              <Typography className='font-medium' color='text.primary'>
                Vue
              </Typography>
              <Typography variant='body2'>Progressive Framework</Typography>
            </div>
          </div>
          <Radio value='vuejs' onChange={handleChange} checked={value === 'vuejs'} />
        </div>
        <div onClick={() => setValue('laravel')} className='flex items-center justify-between cursor-pointer gap-4'>
          <div className='flex items-center gap-4'>
            <CustomAvatar variant='rounded' skin='light' color='warning' size={46}>
              <img src='/images/logos/laravel.png' alt='laravel' height={30} width={30} />
            </CustomAvatar>
            <div className='flex flex-col gap-1'>
              <Typography className='font-medium' color='text.primary'>
                Laravel
              </Typography>
              <Typography variant='body2'>PHP web frameworks</Typography>
            </div>
          </div>
          <Radio value='laravel' onChange={handleChange} checked={value === 'laravel'} />
        </div>
      </div>
      <div className='flex items-center justify-between'>
        <Button
          variant='outlined'
          color='secondary'
          disabled={activeStep === 0}
          onClick={handlePrev}
          startIcon={<DirectionalIcon ltrIconClass='ri-arrow-left-line' rtlIconClass='ri-arrow-right-line' />}
        >
          Previous
        </Button>
        <Button
          variant='contained'
          color={isLastStep ? 'success' : 'primary'}
          onClick={handleNext}
          endIcon={
            isLastStep ? (
              <i className='ri-check-line' />
            ) : (
              <DirectionalIcon ltrIconClass='ri-arrow-right-line' rtlIconClass='ri-arrow-left-line' />
            )
          }
        >
          {isLastStep ? 'Submit' : 'Next'}
        </Button>
      </div>
    </div>
  )
}

export default FrameWork
