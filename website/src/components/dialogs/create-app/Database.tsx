// React Imports
import { useState } from 'react'
import type { ChangeEvent } from 'react'

// MUI Imports
import Typography from '@mui/material/Typography'
import Radio from '@mui/material/Radio'
import TextField from '@mui/material/TextField'
import Button from '@mui/material/Button'

// Component Imports
import DirectionalIcon from '@components/DirectionalIcon'

// Component Imports
import CustomAvatar from '@core/components/mui/Avatar'

// Config Imports
import themeConfig from '@configs/themeConfig'

type Props = {
  activeStep: number
  isLastStep: boolean
  handleNext: () => void
  handlePrev: () => void
}

const DataBase = ({ activeStep, isLastStep, handleNext, handlePrev }: Props) => {
  // States
  const [value, setValue] = useState<string>('firebase')

  const handleChange = (event: ChangeEvent<HTMLInputElement>) => {
    setValue(event.target.value)
  }

  return (
    <div className='flex flex-col gap-6'>
      <TextField
        fullWidth
        label='Database Name'
        placeholder={`${themeConfig.templateName.toLowerCase().replace(/\s+/g, '_')}_database`}
      />
      <div className='flex flex-col gap-4'>
        <Typography variant='h5'>Select Database Engine</Typography>
        <div onClick={() => setValue('firebase')} className='flex items-center justify-between cursor-pointer gap-4'>
          <div className='flex items-center gap-4'>
            <CustomAvatar variant='rounded' skin='light' color='warning' size={46}>
              <img src='/images/logos/firebase.png' alt='firebase' height={30} width={30} />
            </CustomAvatar>
            <div className='flex flex-col gap-1'>
              <Typography className='font-medium' color='text.primary'>
                Firebase
              </Typography>
              <Typography variant='body2'>Cloud Firestore</Typography>
            </div>
          </div>
          <Radio value='firebase' onChange={handleChange} checked={value === 'firebase'} />
        </div>
        <div onClick={() => setValue('aws')} className='flex items-center justify-between cursor-pointer gap-4'>
          <div className='flex items-center gap-4'>
            <CustomAvatar variant='rounded' skin='light' color='secondary' size={46}>
              <img src='/images/logos/aws.png' alt='aws' height={30} width={30} />
            </CustomAvatar>
            <div className='flex flex-col gap-1'>
              <Typography className='font-medium' color='text.primary'>
                AWS
              </Typography>
              <Typography variant='body2'>Amazon Fast NoSQL Database</Typography>
            </div>
          </div>
          <Radio value='aws' onChange={handleChange} checked={value === 'aws'} />
        </div>
        <div onClick={() => setValue('sql')} className='flex items-center justify-between cursor-pointer gap-4'>
          <div className='flex items-center gap-4'>
            <CustomAvatar variant='rounded' skin='light' color='info' size={46}>
              <i className='ri-database-2-line text-3xl' />
            </CustomAvatar>
            <div className='flex flex-col gap-1'>
              <Typography className='font-medium' color='text.primary'>
                MySQL
              </Typography>
              <Typography variant='body2'>Basic MySQL database</Typography>
            </div>
          </div>
          <Radio value='sql' onChange={handleChange} checked={value === 'sql'} />
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

export default DataBase
