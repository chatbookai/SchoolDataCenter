'use client'

// MUI Imports
import Dialog from '@mui/material/Dialog'
import DialogContent from '@mui/material/DialogContent'
import IconButton from '@mui/material/IconButton'

// Type Imports
import type { PricingPlanType } from '@/types/pages/pricingTypes'

// Component Imports
import Pricing from '@components/pricing'

type PricingProps = {
  open: boolean
  setOpen: (open: boolean) => void
  data: PricingPlanType[]
}

const PricingDialog = ({ open, setOpen, data }: PricingProps) => {
  return (
    <Dialog fullWidth maxWidth='lg' open={open} onClose={() => setOpen(false)} scroll='body'>
      <DialogContent className='sm:p-16'>
        <IconButton className='absolute block-start-4 inline-end-4' onClick={() => setOpen(false)}>
          <i className='ri-close-line text-textSecondary' />
        </IconButton>
        <Pricing data={data} />
      </DialogContent>
    </Dialog>
  )
}

export default PricingDialog
