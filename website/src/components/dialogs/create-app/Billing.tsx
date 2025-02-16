// React Imports
import { useState } from 'react'

// MUI Imports
import Grid from '@mui/material/Grid'
import TextField from '@mui/material/TextField'
import FormControlLabel from '@mui/material/FormControlLabel'
import Switch from '@mui/material/Switch'
import Button from '@mui/material/Button'

// Component Imports
import DirectionalIcon from '@components/DirectionalIcon'

type Props = {
  activeStep: number
  isLastStep: boolean
  handleNext: () => void
  handlePrev: () => void
}

const Billing = ({ activeStep, isLastStep, handleNext, handlePrev }: Props) => {
  // States
  const [cardData, setCardData] = useState({
    number: '',
    name: '',
    expiry: '',
    cvv: ''
  })

  return (
    <Grid container spacing={5}>
      <Grid item xs={12}>
        <TextField
          fullWidth
          name='number'
          autoComplete='off'
          label='Card Number'
          placeholder='0000 0000 0000 0000'
          value={cardData.number}
          onChange={e => setCardData({ ...cardData, number: e.target.value })}
        />
      </Grid>
      <Grid item xs={12} sm={6}>
        <TextField
          fullWidth
          name='name'
          label='Name on Card'
          autoComplete='off'
          placeholder='John Doe'
          value={cardData.name}
          onChange={e => setCardData({ ...cardData, name: e.target.value })}
        />
      </Grid>
      <Grid item xs={6} sm={3}>
        <TextField
          fullWidth
          name='expiry'
          autoComplete='off'
          label='Expiry'
          placeholder='MM/YY'
          value={cardData.expiry}
          onChange={e => setCardData({ ...cardData, expiry: e.target.value })}
        />
      </Grid>
      <Grid item xs={6} sm={3}>
        <TextField
          fullWidth
          name='cvv'
          label='CVV'
          autoComplete='off'
          placeholder='123'
          value={cardData.cvv}
          onChange={e => setCardData({ ...cardData, cvv: e.target.value })}
        />
      </Grid>
      <Grid item xs={12}>
        <FormControlLabel control={<Switch defaultChecked />} label='Save Card for future billing?' />
      </Grid>
      <Grid item xs={12} className='flex items-center justify-between'>
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
      </Grid>
    </Grid>
  )
}

export default Billing
