// MUI Imports
import Card from '@mui/material/Card'
import CardHeader from '@mui/material/CardHeader'
import CardContent from '@mui/material/CardContent'
import Divider from '@mui/material/Divider'
import Typography from '@mui/material/Typography'

// Components Imports
import CustomAvatar from '@core/components/mui/Avatar'
import OptionMenu from '@core/components/option-menu'

type DataType = {
  name: string
  profession: string
  totalCourses: number
  avatar: string
}

// Vars
const data: DataType[] = [
  { name: 'Jordan Stevenson', profession: 'Business Intelligence', totalCourses: 33, avatar: '/images/avatars/1.png' },
  { name: 'Bentlee Emblin', profession: 'Digital Marketing', totalCourses: 52, avatar: '/images/avatars/2.png' },
  { name: 'Benedetto Rossiter', profession: 'UI/UX Design', totalCourses: 12, avatar: '/images/avatars/3.png' },
  { name: 'Beverlie Krabbe', profession: 'Vue', totalCourses: 8, avatar: '/images/avatars/4.png' }
]

const PopularInstructors = () => {
  return (
    <Card className='bs-full'>
      <CardHeader
        title='Popular Instructors'
        action={<OptionMenu iconClassName='text-textPrimary' options={['Refresh', 'Update', 'Share']} />}
      />
      <Divider />
      <div className='flex justify-between plb-4 pli-5'>
        <Typography variant='overline'>instructors</Typography>
        <Typography variant='overline'>courses</Typography>
      </div>
      <Divider />
      <CardContent className='flex flex-col gap-4'>
        {data.map((item, i) => (
          <div key={i} className='flex items-center gap-4'>
            <CustomAvatar size={34} src={item.avatar} />
            <div className='flex justify-between items-center is-full gap-4'>
              <div className='flex flex-col gap-1'>
                <Typography className='font-medium' color='text.primary'>
                  {item.name}
                </Typography>
                <Typography variant='caption' color='text.secondary' className='font-normal'>
                  {item.profession}
                </Typography>
              </div>
              <Typography color='text.primary'>{item.totalCourses}</Typography>
            </div>
          </div>
        ))}
      </CardContent>
    </Card>
  )
}

export default PopularInstructors
