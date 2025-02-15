// MUI Imports
import Card from '@mui/material/Card'
import CardHeader from '@mui/material/CardHeader'
import CardContent from '@mui/material/CardContent'
import Typography from '@mui/material/Typography'
import CircularProgress from '@mui/material/CircularProgress'

// Type Imports
import type { ThemeColor } from '@core/types'

// Components Imports
import CustomIconButton from '@core/components/mui/IconButton'
import OptionMenu from '@core/components/option-menu'
import DirectionalIcon from '@components/DirectionalIcon'

type DataType = {
  title: string
  tasks: number
  progress: number
  color: ThemeColor
}

// Vars
const data: DataType[] = [
  { title: 'User Experience Design', tasks: 120, progress: 72, color: 'primary' },
  { title: 'Basic fundamentals', tasks: 32, progress: 48, color: 'success' },
  { title: 'React Native components', tasks: 182, progress: 15, color: 'error' },
  { title: 'Basic of music theory', tasks: 56, progress: 24, color: 'info' }
]

const AssignmentProgress = () => {
  return (
    <Card>
      <CardHeader
        title='Assignment progress'
        action={<OptionMenu iconClassName='text-textPrimary' options={['Refresh', 'Update', 'Share']} />}
      />
      <CardContent className='flex flex-col gap-7 pbs-5'>
        {data.map((item, i) => (
          <div key={i} className='flex items-center gap-4'>
            <div className='relative flex items-center justify-center'>
              <CircularProgress
                variant='determinate'
                size={54}
                value={100}
                thickness={3}
                className='absolute text-[var(--mui-palette-customColors-trackBg)]'
              />
              <CircularProgress
                variant='determinate'
                size={54}
                value={item.progress}
                thickness={3}
                color={item.color}
                sx={{ '& .MuiCircularProgress-circle': { strokeLinecap: 'round' } }}
              />
              <Typography className='absolute font-medium' color='text.primary'>
                {`${item.progress}%`}
              </Typography>
            </div>
            <div className='flex justify-between items-center is-full gap-4'>
              <div className='flex flex-col gap-2'>
                <Typography className='font-medium' color='text.primary'>
                  {item.title}
                </Typography>
                <Typography variant='body2'>{`${item.tasks} Tasks`}</Typography>
              </div>
              <CustomIconButton variant='outlined' color='secondary' className='min-is-fit'>
                <DirectionalIcon ltrIconClass='ri-arrow-right-s-line' rtlIconClass='ri-arrow-left-s-line' />
              </CustomIconButton>
            </div>
          </div>
        ))}
      </CardContent>
    </Card>
  )
}

export default AssignmentProgress
