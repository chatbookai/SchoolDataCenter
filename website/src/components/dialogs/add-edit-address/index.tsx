'use client'

// React Imports
import { useEffect, useState } from 'react'
import type { ChangeEvent } from 'react'

// MUI Imports
import Dialog from '@mui/material/Dialog'
import DialogTitle from '@mui/material/DialogTitle'
import DialogContent from '@mui/material/DialogContent'
import DialogActions from '@mui/material/DialogActions'
import Button from '@mui/material/Button'
import IconButton from '@mui/material/IconButton'
import Typography from '@mui/material/Typography'
import Grid from '@mui/material/Grid'
import TextField from '@mui/material/TextField'
import FormControl from '@mui/material/FormControl'
import InputLabel from '@mui/material/InputLabel'
import Select from '@mui/material/Select'
import MenuItem from '@mui/material/MenuItem'
import Switch from '@mui/material/Switch'
import FormControlLabel from '@mui/material/FormControlLabel'
import { styled } from '@mui/material/styles'
import type { TypographyProps } from '@mui/material/Typography'

// Type Imports
import type { CustomInputHorizontalData } from '@core/components/custom-inputs/types'

// Component Imports
import CustomInputHorizontal from '@core/components/custom-inputs/Horizontal'

type AddEditAddressData = {
  firstName?: string
  lastName?: string
  country?: string
  address1?: string
  address2?: string
  landmark?: string
  city?: string
  state?: string
  zipCode?: string
}

type AddEditAddressProps = {
  open: boolean
  setOpen: (open: boolean) => void
  data?: AddEditAddressData
}

// Vars
const countries = ['Select Country', 'France', 'Russia', 'China', 'UK', 'US']

const initialAddressData: AddEditAddressProps['data'] = {
  firstName: '',
  lastName: '',
  country: '',
  address1: '',
  address2: '',
  landmark: '',
  city: '',
  state: '',
  zipCode: ''
}

// Styled Components
const Title = styled(Typography, {
  name: 'MuiCustomInputVertical',
  slot: 'title'
})<TypographyProps>(({ theme }) => ({
  letterSpacing: '0.15px',
  fontWeight: theme.typography.fontWeightMedium
}))

const customInputData: CustomInputHorizontalData[] = [
  {
    title: (
      <Title component='div' className='flex items-center gap-1'>
        <i className='ri-home-4-line text-xl text-textPrimary' />
        <Typography color='text.primary' className='font-medium'>
          Home
        </Typography>
      </Title>
    ),
    content: 'Delivery Time (7am - 9pm)',
    value: 'home',
    isSelected: true
  },
  {
    title: (
      <Title component='div' className='flex items-center gap-1'>
        <i className='ri-building-4-line text-xl text-textPrimary' />
        <Typography color='text.primary' className='font-medium'>
          Office
        </Typography>
      </Title>
    ),
    content: 'Delivery Time (10am - 6pm)',
    value: 'office'
  }
]

const AddEditAddress = ({ open, setOpen, data }: AddEditAddressProps) => {
  // Vars
  const initialSelected: string = customInputData?.find(item => item.isSelected)?.value || ''

  // States
  const [selected, setSelected] = useState<string>(initialSelected)
  const [addressData, setAddressData] = useState<AddEditAddressProps['data']>(initialAddressData)

  const handleChange = (prop: string | ChangeEvent<HTMLInputElement>) => {
    if (typeof prop === 'string') {
      setSelected(prop)
    } else {
      setSelected((prop.target as HTMLInputElement).value)
    }
  }

  useEffect(() => {
    setAddressData(data ?? initialAddressData)
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [open])

  return (
    <Dialog
      open={open}
      maxWidth='md'
      scroll='body'
      onClose={() => {
        setOpen(false)
        setSelected(initialSelected)
      }}
    >
      <DialogTitle variant='h4' className='flex gap-2 flex-col text-center sm:pbs-16 sm:pbe-6 sm:pli-16'>
        {data ? 'Edit Address' : 'Add New Address'}
        <Typography component='span' className='flex flex-col text-center'>
          {data ? 'Edit Address for future billing' : 'Add address for billing address'}
        </Typography>
      </DialogTitle>
      <form onSubmit={e => e.preventDefault()}>
        <DialogContent className='pbs-0 sm:pli-16'>
          <IconButton onClick={() => setOpen(false)} className='absolute block-start-4 inline-end-4'>
            <i className='ri-close-line text-textSecondary' />
          </IconButton>
          <Grid container spacing={5}>
            {customInputData.map((item, index) => (
              <Grid item xs={12} sm={6} key={index}>
                <CustomInputHorizontal
                  key={index}
                  type='radio'
                  name='addressType'
                  selected={selected}
                  data={item}
                  handleChange={handleChange}
                />
              </Grid>
            ))}
            <Grid item xs={12} sm={6}>
              <TextField
                fullWidth
                label='First Name'
                name='firstName'
                variant='outlined'
                placeholder='John'
                value={addressData?.firstName}
                onChange={e => setAddressData({ ...addressData, firstName: e.target.value })}
              />
            </Grid>
            <Grid item xs={12} sm={6}>
              <TextField
                fullWidth
                label='Last Name'
                name='lastName'
                variant='outlined'
                placeholder='Doe'
                value={addressData?.lastName}
                onChange={e => setAddressData({ ...addressData, lastName: e.target.value })}
              />
            </Grid>
            <Grid item xs={12}>
              <FormControl fullWidth>
                <InputLabel>Country</InputLabel>
                <Select
                  label='Country'
                  name='country'
                  variant='outlined'
                  value={addressData?.country?.toLowerCase().replace(/\s+/g, '-') || ''}
                  onChange={e => setAddressData({ ...addressData, country: e.target.value })}
                >
                  {countries.map((item, index) => (
                    <MenuItem key={index} value={item.toLowerCase().replace(/\s+/g, '-')}>
                      {item}
                    </MenuItem>
                  ))}
                </Select>
              </FormControl>
            </Grid>
            <Grid item xs={12}>
              <TextField
                fullWidth
                label='Address Line 1'
                name='address1'
                variant='outlined'
                placeholder='12, Business Park'
                value={addressData?.address1}
                onChange={e => setAddressData({ ...addressData, address1: e.target.value })}
              />
            </Grid>
            <Grid item xs={12}>
              <TextField
                fullWidth
                label='Address Line 2'
                name='address1'
                variant='outlined'
                placeholder='Mall Road'
                value={addressData?.address2}
                onChange={e => setAddressData({ ...addressData, address2: e.target.value })}
              />
            </Grid>
            <Grid item xs={12} sm={6}>
              <TextField
                fullWidth
                label='Landmark'
                name='landmark'
                variant='outlined'
                placeholder='Nr. Hard Rock Cafe'
                value={addressData?.landmark}
                onChange={e => setAddressData({ ...addressData, landmark: e.target.value })}
              />
            </Grid>
            <Grid item xs={12} sm={6}>
              <TextField
                fullWidth
                label='City'
                name='city'
                variant='outlined'
                placeholder='Los Angeles'
                value={addressData?.city}
                onChange={e => setAddressData({ ...addressData, city: e.target.value })}
              />
            </Grid>
            <Grid item xs={12} sm={6}>
              <TextField
                fullWidth
                label='State'
                name='state'
                variant='outlined'
                placeholder='California'
                value={addressData?.state}
                onChange={e => setAddressData({ ...addressData, state: e.target.value })}
              />
            </Grid>
            <Grid item xs={12} sm={6}>
              <TextField
                fullWidth
                label='Zip Code'
                type='number'
                name='zipCode'
                variant='outlined'
                placeholder='99950'
                value={addressData?.zipCode}
                onChange={e => setAddressData({ ...addressData, zipCode: e.target.value })}
              />
            </Grid>
            <Grid item xs={12}>
              <FormControlLabel control={<Switch defaultChecked />} label='Make this default shipping address' />
            </Grid>
          </Grid>
        </DialogContent>
        <DialogActions className='justify-center pbs-0 sm:pbe-16 sm:pli-16'>
          <Button variant='contained' onClick={() => setOpen(false)} type='submit'>
            {data ? 'Update' : 'Submit'}
          </Button>
          <Button
            variant='outlined'
            color='secondary'
            onClick={() => {
              setOpen(false)
              setSelected(initialSelected)
            }}
            type='reset'
          >
            Cancel
          </Button>
        </DialogActions>
      </form>
    </Dialog>
  )
}

export default AddEditAddress
