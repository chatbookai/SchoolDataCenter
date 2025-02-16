'use client'

// React Imports
import { useState } from 'react'

// MUI Imports
import Grid from '@mui/material/Grid'
import Dialog from '@mui/material/Dialog'
import Button from '@mui/material/Button'
import TextField from '@mui/material/TextField'
import DialogTitle from '@mui/material/DialogTitle'
import DialogContent from '@mui/material/DialogContent'
import DialogActions from '@mui/material/DialogActions'
import FormControl from '@mui/material/FormControl'
import InputLabel from '@mui/material/InputLabel'
import Select from '@mui/material/Select'
import Chip from '@mui/material/Chip'
import MenuItem from '@mui/material/MenuItem'
import Typography from '@mui/material/Typography'
import Switch from '@mui/material/Switch'
import IconButton from '@mui/material/IconButton'
import { FormControlLabel } from '@mui/material'

type EditUserInfoData = {
  firstName?: string
  lastName?: string
  userName?: string
  billingEmail?: string
  status?: string
  taxId?: string
  contact?: string
  language?: string[]
  country?: string
  useAsBillingAddress?: boolean
}

type EditUserInfoProps = {
  open: boolean
  setOpen: (open: boolean) => void
  data?: EditUserInfoData
}

// Vars
const initialData: EditUserInfoProps['data'] = {
  firstName: 'Oliver',
  lastName: 'Queen',
  userName: 'oliverQueen',
  billingEmail: 'oliverQueen@gmail.com',
  status: 'status',
  taxId: 'Tax-8894',
  contact: '+ 1 609 933 4422',
  language: ['english'],
  country: 'US',
  useAsBillingAddress: true
}

const status = ['Status', 'Active', 'Inactive', 'Suspended']

const languages = ['English', 'Spanish', 'French', 'German', 'Hindi']

const countries = ['Select Country', 'France', 'Russia', 'China', 'UK', 'US']

const EditUserInfo = ({ open, setOpen, data }: EditUserInfoProps) => {
  // States
  const [userData, setUserData] = useState<EditUserInfoProps['data']>(data || initialData)

  const handleClose = () => {
    setOpen(false)
    setUserData(data || initialData)
  }

  return (
    <Dialog fullWidth open={open} onClose={handleClose} maxWidth='md' scroll='body'>
      <DialogTitle variant='h4' className='flex gap-2 flex-col items-center sm:pbs-16 sm:pbe-6 sm:pli-16'>
        <div className='max-sm:is-[80%] max-sm:text-center'>Edit User Information</div>
        <Typography component='span' className='flex flex-col text-center'>
          Updating user details will receive a privacy audit.
        </Typography>
      </DialogTitle>
      <form onSubmit={e => e.preventDefault()}>
        <DialogContent className='overflow-visible pbs-0 sm:pli-16'>
          <IconButton onClick={handleClose} className='absolute block-start-4 inline-end-4'>
            <i className='ri-close-line text-textSecondary' />
          </IconButton>
          <Grid container spacing={5}>
            <Grid item xs={12} sm={6}>
              <TextField
                fullWidth
                label='First Name'
                placeholder='John'
                value={userData?.firstName}
                onChange={e => setUserData({ ...userData, firstName: e.target.value })}
              />
            </Grid>
            <Grid item xs={12} sm={6}>
              <TextField
                fullWidth
                label='Last Name'
                placeholder='Doe'
                value={userData?.lastName}
                onChange={e => setUserData({ ...userData, lastName: e.target.value })}
              />
            </Grid>
            <Grid item xs={12}>
              <TextField
                fullWidth
                label='User Name'
                placeholder='JohnDoe'
                value={userData?.userName}
                onChange={e => setUserData({ ...userData, userName: e.target.value })}
              />
            </Grid>
            <Grid item xs={12} sm={6}>
              <TextField
                fullWidth
                label='Billing Email'
                placeholder='johnDoe@email.com'
                value={userData?.billingEmail}
                onChange={e => setUserData({ ...userData, billingEmail: e.target.value })}
              />
            </Grid>
            <Grid item xs={12} sm={6}>
              <FormControl fullWidth>
                <InputLabel>Status</InputLabel>
                <Select
                  label='Status'
                  value={userData?.status}
                  onChange={e => setUserData({ ...userData, status: e.target.value as string })}
                >
                  {status.map((status, index) => (
                    <MenuItem key={index} value={status.toLowerCase().replace(/\s+/g, '-')}>
                      {status}
                    </MenuItem>
                  ))}
                </Select>
              </FormControl>
            </Grid>
            <Grid item xs={12} sm={6}>
              <TextField
                fullWidth
                label='Tax ID'
                placeholder='Tax-7490'
                value={userData?.taxId}
                onChange={e => setUserData({ ...userData, taxId: e.target.value })}
              />
            </Grid>
            <Grid item xs={12} sm={6}>
              <TextField
                fullWidth
                label='Contact'
                placeholder='+ 123 456 7890'
                value={userData?.contact}
                onChange={e => setUserData({ ...userData, contact: e.target.value })}
              />
            </Grid>
            <Grid item xs={12} sm={6}>
              <FormControl fullWidth>
                <InputLabel>Language</InputLabel>
                <Select
                  label='Language'
                  multiple
                  value={userData?.language?.map(lang => lang.toLowerCase().replace(/\s+/g, '-')) || []}
                  onChange={e => setUserData({ ...userData, language: e.target.value as string[] })}
                  renderValue={selected => (
                    <div className='flex items-center gap-2 flex-wrap'>
                      {(selected as string[]).map(value => (
                        <Chip key={value} label={value} className='capitalize' size='small' />
                      ))}
                    </div>
                  )}
                >
                  {languages.map((language, index) => (
                    <MenuItem key={index} value={language.toLowerCase().replace(/\s+/g, '-')}>
                      {language}
                    </MenuItem>
                  ))}
                </Select>
              </FormControl>
            </Grid>
            <Grid item xs={12} sm={6}>
              <FormControl fullWidth>
                <InputLabel>Country</InputLabel>
                <Select
                  label='Country'
                  value={userData?.country?.toLowerCase().replace(/\s+/g, '-')}
                  onChange={e => setUserData({ ...userData, country: e.target.value as string })}
                >
                  {countries.map((country, index) => (
                    <MenuItem key={index} value={country.toLowerCase().replace(/\s+/g, '-')}>
                      {country}
                    </MenuItem>
                  ))}
                </Select>
              </FormControl>
            </Grid>
            <Grid item xs={12}>
              <FormControlLabel
                control={<Switch defaultChecked={userData?.useAsBillingAddress} />}
                label='Use as a billing address?'
              />
            </Grid>
          </Grid>
        </DialogContent>
        <DialogActions className='justify-center pbs-0 sm:pbe-16 sm:pli-16'>
          <Button variant='contained' onClick={handleClose} type='submit'>
            Submit
          </Button>
          <Button variant='outlined' color='secondary' type='reset' onClick={handleClose}>
            Cancel
          </Button>
        </DialogActions>
      </form>
    </Dialog>
  )
}

export default EditUserInfo
