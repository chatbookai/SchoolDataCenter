'use client'

// MUI Imports
import Dialog from '@mui/material/Dialog'
import DialogTitle from '@mui/material/DialogTitle'
import DialogContent from '@mui/material/DialogContent'
import IconButton from '@mui/material/IconButton'
import Typography from '@mui/material/Typography'
import Avatar from '@mui/material/Avatar'
import { useColorScheme } from '@mui/material/styles'

// Third-party Imports
import classnames from 'classnames'

type PaymentMethodProps = {
  open: boolean
  setOpen: (open: boolean) => void
}

type CardList = {
  image: string
  imgWidth: string
  imgHeight?: string
  alt: string
  cardName: string
  cardType: string
}

// Vars
const cardList: CardList[] = [
  {
    image: '/images/logos/visa.png',
    imgWidth: '30px',
    alt: 'visa card',
    cardName: 'Visa',
    cardType: 'Credit Card'
  },
  {
    image: '/images/logos/american-express.png',
    imgWidth: '36px',
    alt: 'American Express',
    cardName: 'American Express',
    cardType: 'Credit Card'
  },
  {
    image: '/images/logos/mastercard.png',
    imgWidth: '30px',
    alt: 'Mastercard',
    cardName: 'Mastercard',
    cardType: 'Credit Card'
  },
  {
    image: '/images/logos/jcb.png',
    imgWidth: '21.4px',
    alt: 'JCB',
    cardName: 'JCB',
    cardType: 'Credit Card'
  },
  {
    image: '/images/logos/dinners-club.png',
    imgWidth: '20px',
    alt: 'Dinners Club',
    cardName: 'Dinners Club',
    cardType: 'Credit Card'
  }
]

const PaymentMethod = ({ open, setOpen }: PaymentMethodProps) => {
  // Hooks
  const { mode } = useColorScheme()

  return (
    <Dialog fullWidth open={open} onClose={() => setOpen(false)} maxWidth='sm' scroll='body'>
      <DialogTitle variant='h4' className='flex gap-2 flex-col text-center sm:pbs-16 sm:pbe-10 sm:pli-16'>
        Select Payment Methods
        <Typography component='span' className='flex flex-col items-center'>
          Supported payment methods
        </Typography>
      </DialogTitle>
      <DialogContent className='pbs-0 sm:pli-16 sm:pbe-20'>
        <IconButton onClick={() => setOpen(false)} className='absolute block-start-4 inline-end-4'>
          <i className='ri-close-line text-textSecondary' />
        </IconButton>
        <div>
          {cardList?.map((card, index) => (
            <div
              key={index}
              className='flex gap-x-4 gap-y-1 flex-col sm:flex-row !items-start sm:items-center justify-between first:pbe-4 last:pbs-4 [&:not(:last-child):not(:first-child)]:plb-4 [&:not(:last-child)]:border-be'
            >
              <div className='flex items-center gap-4'>
                <Avatar
                  variant='rounded'
                  className={classnames('is-[50px] bs-[30px]', {
                    'bg-white': mode === 'dark',
                    'bg-actionHover': mode === 'light'
                  })}
                >
                  <img src={card.image} alt={card.alt} height={card.imgHeight} width={card.imgWidth} />
                </Avatar>
                <Typography className='font-medium' color='text.primary'>
                  {card.cardName}
                </Typography>
              </div>
              <Typography className='hidden sm:block'>{card.cardType}</Typography>
            </div>
          ))}
        </div>
      </DialogContent>
    </Dialog>
  )
}

export default PaymentMethod
