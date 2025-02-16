// MUI Imports
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import Chip from '@mui/material/Chip'
import Typography from '@mui/material/Typography'

// Type Imports
import type { CardStatsCustomerStatsProps } from '@/types/pages/widgetTypes'

// Component Imports
import CustomAvatar from '@core/components/mui/Avatar'

const CustomerStats = (props: CardStatsCustomerStatsProps) => {
  // Props
  const { title, avatarIcon, color, description, stats, content, chipLabel } = props

  return (
    <Card>
      <CardContent className='flex flex-col gap-2'>
        <CustomAvatar variant='rounded' skin='light' color={color}>
          <i className={avatarIcon} />
        </CustomAvatar>
        <Typography variant='h5' className='capitalize'>
          {title}
        </Typography>

        <div className='flex flex-col items-start'>
          {stats ? (
            <div className='flex items-center gap-1'>
              <Typography variant='h5' color={`${color}.main`}>
                {stats}
              </Typography>
              <Typography>{content}</Typography>
            </div>
          ) : (
            <Chip variant='tonal' label={chipLabel} color={color} size='small' className='mbe-1' />
          )}
          <Typography>{description}</Typography>
        </div>
      </CardContent>
    </Card>
  )
}

export default CustomerStats
