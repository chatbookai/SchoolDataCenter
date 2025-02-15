'use client'

// MUI Imports
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import Chip from '@mui/material/Chip'
import Divider from '@mui/material/Divider'
import List from '@mui/material/List'
import ListItem from '@mui/material/ListItem'
import Typography from '@mui/material/Typography'
import useMediaQuery from '@mui/material/useMediaQuery'
import { useTheme } from '@mui/material/styles'

// Third-party Imports
import ReactPlayer from '@/libs/ReactPlayer'

// Type Imports
import type { CourseDetails } from '@/types/apps/academyTypes'

// Components Imports
import CustomAvatar from '@core/components/mui/Avatar'
import CustomIconButton from '@core/components/mui/IconButton'

const Details = ({ data }: { data?: CourseDetails }) => {
  // Hooks
  const theme = useTheme()
  const smallScreen = useMediaQuery(theme.breakpoints.down('sm'))

  return (
    <Card>
      <CardContent className='flex flex-wrap items-center justify-between gap-4 pbe-6'>
        <div>
          <Typography variant='h5'>UI/UX Basic Fundamentals</Typography>
          <Typography>
            Prof. <span className='font-medium text-textPrimary'>Devonne Wallbridge</span>
          </Typography>
        </div>
        <div className='flex items-center gap-4'>
          <Chip label='UI/UX' variant='tonal' size='small' color='error' />
          <i className='ri-share-forward-line cursor-pointer' />
          <i className='ri-bookmark-line cursor-pointer' />
        </div>
      </CardContent>
      <CardContent>
        <div className='border rounded'>
          <div className='mli-2 mbs-2 overflow-hidden rounded'>
            <ReactPlayer
              playing
              controls
              url='https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-576p.mp4'
              height={smallScreen ? 280 : 440}
              className='bg-black !is-full'
              light={
                <img
                  src='/images/apps/academy/4.png'
                  alt='Thumbnail'
                  className='is-full bs-full object-cover bg-backgroundPaper'
                />
              }
              playIcon={
                <CustomIconButton variant='contained' color='error' className='absolute rounded-full'>
                  <i className='ri-play-line text-2xl' />
                </CustomIconButton>
              }
            />
          </div>
          <div className='flex flex-col gap-6 p-5'>
            <div className='flex flex-col gap-4'>
              <Typography variant='h5'>About this course</Typography>
              <Typography>{data?.about}</Typography>
            </div>
            <Divider />
            <div className='flex flex-col gap-4'>
              <Typography variant='h5'>By the numbers</Typography>
              <div className='flex flex-wrap gap-x-12 gap-y-2'>
                <List role='list' component='div' className='flex flex-col gap-2 plb-0'>
                  <ListItem role='listitem' className='flex items-center gap-2 p-0'>
                    <i className='ri-check-line text-xl text-textSecondary' />
                    <Typography>Skill level: {data?.skillLevel}</Typography>
                  </ListItem>
                  <ListItem role='listitem' className='flex items-center gap-2 p-0'>
                    <i className='ri-group-line text-xl text-textSecondary' />
                    <Typography>Students: {data?.totalStudents.toLocaleString()}</Typography>
                  </ListItem>
                  <ListItem role='listitem' className='flex items-center gap-2 p-0'>
                    <i className='ri-global-line text-xl text-textSecondary' />
                    <Typography>Languages: {data?.language}</Typography>
                  </ListItem>
                  <ListItem role='listitem' className='flex items-center gap-2 p-0'>
                    <i className='ri-pages-line text-xl text-textSecondary' />
                    <Typography>Captions: {data?.isCaptions ? 'Yes' : 'No'}</Typography>
                  </ListItem>
                </List>
                <List role='list' component='div' className='flex flex-col gap-2 plb-0'>
                  <ListItem role='listitem' className='flex items-center gap-2 p-0'>
                    <i className='ri-video-upload-line text-xl text-textSecondary' />
                    <Typography>Lectures: {data?.totalLectures}</Typography>
                  </ListItem>
                  <ListItem role='listitem' className='flex items-center gap-2 p-0'>
                    <i className='ri-time-line text-xl text-textSecondary' />
                    <Typography>Video: {data?.length}</Typography>
                  </ListItem>
                </List>
              </div>
            </div>
            <Divider />
            <div className='flex flex-col gap-4'>
              <Typography variant='h5'>Description</Typography>
              {data?.description.map((value, index) => <Typography key={index}>{value}</Typography>)}
            </div>
            <Divider />
            <div className='flex flex-col gap-4'>
              <Typography variant='h5'>Instructor</Typography>
              <div className='flex items-center gap-4'>
                <CustomAvatar skin='light-static' color='error' src={data?.instructorAvatar} size={38} />
                <div className='flex flex-col gap-1'>
                  <Typography className='font-medium' color='text.primary'>
                    {data?.instructor}
                  </Typography>
                  <Typography variant='body2'>{data?.instructorPosition}</Typography>
                </div>
              </div>
            </div>
          </div>
        </div>
      </CardContent>
    </Card>
  )
}

export default Details
