'use client'

// React Imports
import type { ReactNode } from 'react'

// Next Imports
import dynamic from 'next/dynamic'

// MUI Imports
import Divider from '@mui/material/Divider'
import Chip from '@mui/material/Chip'
import Typography from '@mui/material/Typography'
import useMediaQuery from '@mui/material/useMediaQuery'
import { lighten, darken, useTheme } from '@mui/material/styles'

// Third-party Imports
import type { ApexOptions } from 'apexcharts'

// Type Imports
import type { ThemeColor } from '@core/types'

// Component Imports
import CustomAvatar from '@core/components/mui/Avatar'

// Styled Component Imports
const AppReactApexCharts = dynamic(() => import('@/libs/styles/AppReactApexCharts'))

type DataType = {
  title: string
  value: string
  color: ThemeColor
  icon: ReactNode
}

// Vars
const data: DataType[] = [
  {
    title: 'Hours Spent',
    value: '34h',
    color: 'primary',
    icon: (
      <svg xmlns='http://www.w3.org/2000/svg' width='38' height='38' viewBox='0 0 38 38' fill='none'>
        <path
          opacity='0.2'
          d='M5.9375 26.125V10.6875C5.9375 10.0576 6.18772 9.45352 6.63312 9.00812C7.07852 8.56272 7.68261 8.3125 8.3125 8.3125H29.6875C30.3174 8.3125 30.9215 8.56272 31.3669 9.00812C31.8123 9.45352 32.0625 10.0576 32.0625 10.6875V26.125H5.9375Z'
          fill='currentColor'
        />
        <path
          d='M5.9375 26.125V10.6875C5.9375 10.0576 6.18772 9.45352 6.63312 9.00812C7.07852 8.56272 7.68261 8.3125 8.3125 8.3125H29.6875C30.3174 8.3125 30.9215 8.56272 31.3669 9.00812C31.8123 9.45352 32.0625 10.0576 32.0625 10.6875V26.125M21.375 13.0625H16.625M3.5625 26.125H34.4375V28.5C34.4375 29.1299 34.1873 29.734 33.7419 30.1794C33.2965 30.6248 32.6924 30.875 32.0625 30.875H5.9375C5.30761 30.875 4.70352 30.6248 4.25812 30.1794C3.81272 29.734 3.5625 29.1299 3.5625 28.5V26.125Z'
          stroke='currentColor'
          strokeWidth='2'
          strokeLinecap='round'
          strokeLinejoin='round'
        />
      </svg>
    )
  },
  {
    title: 'Test Results',
    value: '82%',
    color: 'info',
    icon: (
      <svg xmlns='http://www.w3.org/2000/svg' width='38' height='38' viewBox='0 0 38 38' fill='none'>
        <path
          opacity='0.2'
          d='M11.682 24.7885C10.2683 23.6892 9.1233 22.2826 8.33376 20.6753C7.54423 19.0679 7.13087 17.3019 7.125 15.5111C7.09532 9.06896 12.2758 3.71037 18.718 3.56193C21.2112 3.50283 23.6598 4.2302 25.7164 5.6409C27.7731 7.05159 29.3334 9.07399 30.176 11.4213C31.0187 13.7686 31.1009 16.3216 30.4111 18.7182C29.7213 21.1149 28.2944 23.2335 26.3328 24.7736C25.8995 25.1086 25.5485 25.5382 25.3067 26.0296C25.0648 26.521 24.9386 27.0611 24.9375 27.6088V28.4994C24.9375 28.8144 24.8124 29.1164 24.5897 29.3391C24.367 29.5618 24.0649 29.6869 23.75 29.6869H14.25C13.9351 29.6869 13.633 29.5618 13.4103 29.3391C13.1876 29.1164 13.0625 28.8144 13.0625 28.4994V27.6088C13.0588 27.0652 12.9328 26.5295 12.6938 26.0413C12.4548 25.553 12.109 25.1249 11.682 24.7885Z'
          fill='currentColor'
        />
        <path
          fillRule='evenodd'
          clipRule='evenodd'
          d='M25.1507 6.46554C23.2672 5.17364 21.0249 4.50752 18.7416 4.56165L18.7409 4.56167C18.4981 4.56726 18.2571 4.58096 18.0184 4.6025L18.6948 2.5622C21.3978 2.49826 24.0523 3.28688 26.282 4.81625C28.5118 6.34574 30.2035 8.53844 31.1171 11.0834C32.0307 13.6283 32.1199 16.3963 31.372 18.9948C30.6241 21.5933 29.077 23.8903 26.9503 25.5602L26.9443 25.5649L26.9443 25.5648C26.6316 25.8065 26.3783 26.1165 26.2038 26.4711C26.0293 26.8257 25.9382 27.2155 25.9374 27.6107V28.4994C25.9374 29.0796 25.7069 29.636 25.2967 30.0462C24.8865 30.4565 24.3301 30.6869 23.7499 30.6869H14.2499C13.6697 30.6869 13.1133 30.4565 12.7031 30.0462C12.2929 29.636 12.0624 29.0796 12.0624 28.4994V27.6125C12.0592 27.2201 11.968 26.8334 11.7955 26.4809C11.6229 26.1283 11.3734 25.819 11.0654 25.5758L11.7412 23.5373C11.9205 23.6971 12.1055 23.8511 12.2958 23.9991L11.6819 24.7885L12.3008 24.003C12.8456 24.4322 13.2869 24.9786 13.5919 25.6016C13.8968 26.2247 14.0576 26.9083 14.0624 27.602L14.0624 27.6088L14.0624 28.4994C14.0624 28.5492 14.0822 28.5969 14.1173 28.632C14.1525 28.6672 14.2002 28.6869 14.2499 28.6869H23.7499C23.7996 28.6869 23.8473 28.6672 23.8825 28.632C23.9176 28.5969 23.9374 28.5492 23.9374 28.4994V27.6088L23.9374 27.6069C23.9388 26.9067 24.1002 26.2162 24.4093 25.588C24.7179 24.961 25.1655 24.4128 25.7179 23.985C27.5129 22.5747 28.8186 20.6353 29.45 18.4416C30.0817 16.2468 30.0064 13.9088 29.2347 11.7592C28.463 9.60954 27.0341 7.75744 25.1507 6.46554ZM11.7411 23.5373L11.7412 23.5373L18.0184 4.6025L18.0178 4.60255L18.6942 2.56221C11.7041 2.72363 6.09308 8.5318 6.12491 15.5151C6.13137 17.4574 6.57975 19.3728 7.43609 21.1162C8.29203 22.8587 9.53309 24.3837 11.0654 25.5758L11.7411 23.5373ZM11.7411 23.5373C10.7006 22.6103 9.84758 21.4892 9.23122 20.2344C8.50859 18.7632 8.13026 17.1469 8.12489 15.5079L8.12489 15.5065C8.09882 9.84932 12.4635 5.10401 18.0178 4.60255L11.7411 23.5373ZM12.0625 34.437C12.0625 33.8847 12.5102 33.437 13.0625 33.437H24.9375C25.4898 33.437 25.9375 33.8847 25.9375 34.437C25.9375 34.9892 25.4898 35.437 24.9375 35.437H13.0625C12.5102 35.437 12.0625 34.9892 12.0625 34.437ZM20.3695 7.44477C19.825 7.35247 19.3087 7.71906 19.2164 8.26357C19.1241 8.80809 19.4907 9.32434 20.0352 9.41664C21.2825 9.62807 22.4333 10.2214 23.329 11.1148C24.2247 12.0082 24.821 13.1576 25.0356 14.4043C25.1293 14.9485 25.6465 15.3138 26.1907 15.2201C26.735 15.1264 27.1003 14.6092 27.0066 14.065C26.7217 12.4102 25.9303 10.8846 24.7414 9.69879C23.5526 8.51298 22.025 7.72541 20.3695 7.44477Z'
          fill='currentColor'
        />
      </svg>
    )
  },
  {
    title: 'Course Completed',
    value: '14',
    color: 'warning',
    icon: (
      <svg xmlns='http://www.w3.org/2000/svg' width='38' height='38' viewBox='0 0 38 38' fill='none'>
        <path
          opacity='0.2'
          d='M8.08984 29.9102C6.72422 28.5445 7.62969 25.6797 6.93203 24.0023C6.23438 22.325 3.5625 20.8555 3.5625 19C3.5625 17.1445 6.20469 15.7344 6.93203 13.9977C7.65938 12.2609 6.72422 9.45547 8.08984 8.08984C9.45547 6.72422 12.3203 7.62969 13.9977 6.93203C15.675 6.23438 17.1445 3.5625 19 3.5625C20.8555 3.5625 22.2656 6.20469 24.0023 6.93203C25.7391 7.65938 28.5445 6.72422 29.9102 8.08984C31.2758 9.45547 30.3703 12.3203 31.068 13.9977C31.7656 15.675 34.4375 17.1445 34.4375 19C34.4375 20.8555 31.7953 22.2656 31.068 24.0023C30.3406 25.7391 31.2758 28.5445 29.9102 29.9102C28.5445 31.2758 25.6797 30.3703 24.0023 31.068C22.325 31.7656 20.8555 34.4375 19 34.4375C17.1445 34.4375 15.7344 31.7953 13.9977 31.068C12.2609 30.3406 9.45547 31.2758 8.08984 29.9102Z'
          fill='currentColor'
        />
        <path
          d='M25.5312 15.4375L16.818 23.75L12.4687 19.5937M8.08984 29.9102C6.72422 28.5445 7.62969 25.6797 6.93203 24.0023C6.23437 22.325 3.5625 20.8555 3.5625 19C3.5625 17.1445 6.20469 15.7344 6.93203 13.9977C7.65937 12.2609 6.72422 9.45547 8.08984 8.08984C9.45547 6.72422 12.3203 7.62969 13.9977 6.93203C15.675 6.23437 17.1445 3.5625 19 3.5625C20.8555 3.5625 22.2656 6.20469 24.0023 6.93203C25.7391 7.65937 28.5445 6.72422 29.9102 8.08984C31.2758 9.45547 30.3703 12.3203 31.068 13.9977C31.7656 15.675 34.4375 17.1445 34.4375 19C34.4375 20.8555 31.7953 22.2656 31.068 24.0023C30.3406 25.7391 31.2758 28.5445 29.9102 29.9102C28.5445 31.2758 25.6797 30.3703 24.0023 31.068C22.325 31.7656 20.8555 34.4375 19 34.4375C17.1445 34.4375 15.7344 31.7953 13.9977 31.068C12.2609 30.3406 9.45547 31.2758 8.08984 29.9102Z'
          stroke='currentColor'
          strokeWidth='2'
          strokeLinecap='round'
          strokeLinejoin='round'
        />
      </svg>
    )
  }
]

const WelcomeCard = () => {
  // Hooks
  const theme = useTheme()
  const belowMdScreen = useMediaQuery(theme.breakpoints.down('md'))

  // Vars
  const options: ApexOptions = {
    chart: {
      sparkline: { enabled: true }
    },
    grid: {
      padding: {
        left: 20,
        right: 20
      }
    },
    colors: [
      darken(theme.palette.success.main, 0.15),
      darken(theme.palette.success.main, 0.1),
      'var(--mui-palette-success-main)',
      lighten(theme.palette.success.main, 0.2),
      lighten(theme.palette.success.main, 0.4),
      lighten(theme.palette.success.main, 0.6)
    ],
    stroke: { width: 0 },
    legend: { show: false },
    tooltip: { theme: 'false' },
    dataLabels: { enabled: false },
    labels: ['36h', '56h', '16h', '32h', '56h', '16h'],
    states: {
      hover: {
        filter: { type: 'none' }
      },
      active: {
        filter: { type: 'none' }
      }
    },
    plotOptions: {
      pie: {
        customScale: 0.9,
        donut: {
          size: '70%',
          labels: {
            show: true,
            name: {
              offsetY: 20,
              fontSize: '0.875rem'
            },
            value: {
              offsetY: -15,
              fontWeight: 500,
              fontSize: '1.125rem',
              formatter: value => `${value}%`,
              color: 'var(--mui-palette-text-primary)'
            },
            total: {
              show: true,
              fontSize: '0.8125rem',
              label: 'Total',
              color: 'var(--mui-palette-text-disabled)',
              formatter: () => '231h'
            }
          }
        }
      }
    }
  }

  return (
    <div className='flex max-md:flex-col md:items-center gap-6 plb-5'>
      <div className='md:is-8/12'>
        <div className='flex items-baseline gap-1 mbe-2'>
          <Typography variant='h5'>Welcome back,</Typography>
          <Typography variant='h4'>Felecia 👋🏻</Typography>
        </div>
        <div className='mbe-4'>
          <Typography>Your progress this week is Awesome. let&apos;s keep it up</Typography>
          <Typography>and get a lot of points reward!</Typography>
        </div>
        <div className='flex flex-wrap max-md:flex-col justify-between gap-6'>
          {data.map((item, i) => (
            <div key={i} className='flex gap-4'>
              <CustomAvatar variant='rounded' skin='light' size={54} color={item.color}>
                {item.icon}
              </CustomAvatar>
              <div>
                <Typography className='font-medium'>{item.title}</Typography>
                <Typography variant='h4' color={`${item.color}.main`}>
                  {item.value}
                </Typography>
              </div>
            </div>
          ))}
        </div>
      </div>
      <Divider orientation={belowMdScreen ? 'horizontal' : 'vertical'} flexItem />
      <div className='flex justify-between md:is-4/12'>
        <div className='flex flex-col justify-between gap-6'>
          <div>
            <Typography variant='h5' className='mbe-1'>
              Time spendings
            </Typography>
            <Typography>Weekly report</Typography>
          </div>
          <div>
            <Typography variant='h4' className='mbe-2'>
              231<span className='text-textSecondary'>h</span> 14<span className='text-textSecondary'>m</span>
            </Typography>
            <Chip label='+18.4%' variant='tonal' size='small' color='success' />
          </div>
        </div>
        <AppReactApexCharts type='donut' height={230} width={209} options={options} series={[23, 35, 10, 20, 35, 23]} />
      </div>
    </div>
  )
}

export default WelcomeCard
