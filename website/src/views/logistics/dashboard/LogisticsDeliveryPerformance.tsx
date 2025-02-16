// MUI Imports
import Card from '@mui/material/Card'
import CardHeader from '@mui/material/CardHeader'
import CardContent from '@mui/material/CardContent'
import Typography from '@mui/material/Typography'

// Third-party Imports
import classnames from 'classnames'

// Components Imports
import OptionMenu from '@core/components/option-menu'
import CustomAvatar from '@core/components/mui/Avatar'

// Types Imports
import type { ThemeColor } from '@core/types'

type dataTypes = {
  title: string
  value: string
  change: number
  icon: string
  color: ThemeColor
}

const deliveryData: dataTypes[] = [
  { title: 'Packages in transit', value: '10k', change: 25.8, icon: 'ri-gift-line', color: 'primary' },
  { title: 'Packages out for delivery', value: '5k', change: 4.3, icon: 'ri-car-line', color: 'info' },
  { title: 'Packages delivered', value: '15k', change: -12.5, icon: 'ri-check-line', color: 'success' },
  { title: 'Delivery success rate', value: '95%', change: 35.6, icon: 'ri-home-6-line', color: 'warning' },
  { title: 'Average delivery time', value: '2.5 Days', change: -2.15, icon: 'ri-time-line', color: 'secondary' },
  { title: 'Customer satisfaction', value: '4.5/5', change: 5.7, icon: 'ri-user-3-line', color: 'error' }
]

const LogisticsDeliveryPerformance = () => {
  return (
    <Card>
      <CardHeader
        title='Delivery Performance'
        subheader='12% increase in this month'
        action={<OptionMenu iconClassName='text-textPrimary' options={['Select All', 'Refresh', 'Share']} />}
      />
      <CardContent className='flex flex-col gap-6'>
        {deliveryData.map((data, index) => (
          <div key={index} className='flex items-center gap-4'>
            <CustomAvatar skin='light' color={data.color} variant='rounded'>
              <i className={data.icon} />
            </CustomAvatar>
            <div className='flex justify-between items-center gap-4 is-full'>
              <div>
                <Typography color='text.primary' className='line-clamp-1'>
                  {data.title}
                </Typography>
                <div className='flex items-center gap-1'>
                  <i
                    className={classnames(
                      data.change > 0 ? 'ri-arrow-up-s-line text-success' : 'ri-arrow-down-s-line text-error'
                    )}
                  />
                  <Typography color={data.change > 0 ? 'success.main' : 'error.main'}>{data.change}%</Typography>
                </div>
              </div>
              <Typography color='text.primary' className='font-medium text-nowrap'>
                {data.value}
              </Typography>
            </div>
          </div>
        ))}
      </CardContent>
    </Card>
  )
}

export default LogisticsDeliveryPerformance
