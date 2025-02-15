// React Imports
import type { ChangeEvent } from 'react'
import { useState, useEffect } from 'react'

// Next Imports
import Link from 'next/link'

// MUI Imports
import Grid from '@mui/material/Grid'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import Chip from '@mui/material/Chip'
import Button from '@mui/material/Button'
import FormControl from '@mui/material/FormControl'
import FormControlLabel from '@mui/material/FormControlLabel'
import InputLabel from '@mui/material/InputLabel'
import LinearProgress from '@mui/material/LinearProgress'
import MenuItem from '@mui/material/MenuItem'
import Pagination from '@mui/material/Pagination'
import Select from '@mui/material/Select'
import Switch from '@mui/material/Switch'
import Typography from '@mui/material/Typography'

// Type Imports
import type { Course } from '@/types/apps/academyTypes'
import type { ThemeColor } from '@core/types'

// Component Imports
import DirectionalIcon from '@components/DirectionalIcon'

// Util Imports

type ChipColorType = {
  color: ThemeColor
}

type Props = {
  courseData?: Course[]
  searchValue: string
}

const chipColor: { [key: string]: ChipColorType } = {
  Web: { color: 'primary' },
  Art: { color: 'success' },
  'UI/UX': { color: 'error' },
  Psychology: { color: 'warning' },
  Design: { color: 'info' }
}

const Courses = (props: Props) => {
  // Props
  const { courseData, searchValue } = props

  // States
  const [course, setCourse] = useState<Course['tags']>('All')
  const [hideCompleted, setHideCompleted] = useState(true)
  const [data, setData] = useState<Course[]>([])
  const [activePage, setActivePage] = useState(0)

  useEffect(() => {
    let newData =
      courseData?.filter(courseItem => {
        if (course === 'All') return !hideCompleted || courseItem.completedTasks !== courseItem.totalTasks

        return courseItem.tags === course && (!hideCompleted || courseItem.completedTasks !== courseItem.totalTasks)
      }) ?? []

    if (searchValue) {
      newData = newData.filter(category => category.courseTitle.toLowerCase().includes(searchValue.toLowerCase()))
    }

    if (activePage > Math.ceil(newData.length / 6)) setActivePage(0)

    setData(newData)
  }, [searchValue, activePage, course, hideCompleted, courseData])

  const handleChange = (e: ChangeEvent<HTMLInputElement>) => {
    setHideCompleted(e.target.checked)
    setActivePage(0)
  }

  return (
    <Card>
      <CardContent className='flex flex-col gap-6'>
        <div className='flex flex-wrap items-center justify-between gap-4'>
          <div>
            <Typography variant='h5'>My Courses</Typography>
            <Typography>Total 6 course you have purchased</Typography>
          </div>
          <div className='flex flex-wrap items-center gap-y-4 gap-x-6'>
            <FormControl fullWidth size='small' className='is-[250px] flex-auto'>
              <InputLabel id='course-select'>Courses</InputLabel>
              <Select
                fullWidth
                id='select-course'
                value={course}
                onChange={e => {
                  setCourse(e.target.value)
                  setActivePage(0)
                }}
                label='Courses'
                labelId='course-select'
              >
                <MenuItem value='All'>All Courses</MenuItem>
                <MenuItem value='Web'>Web</MenuItem>
                <MenuItem value='Art'>Art</MenuItem>
                <MenuItem value='UI/UX'>UI/UX</MenuItem>
                <MenuItem value='Psychology'>Psychology</MenuItem>
                <MenuItem value='Design'>Design</MenuItem>
              </Select>
            </FormControl>
            <FormControlLabel
              control={<Switch onChange={handleChange} checked={hideCompleted} />}
              label='Hide completed'
            />
          </div>
        </div>
        {data.length > 0 ? (
          <Grid container spacing={6}>
            {data.slice(activePage * 6, activePage * 6 + 6).map((item, index) => (
              <Grid item xs={12} sm={6} md={4} key={index}>
                <div className='border rounded bs-full'>
                  <div className='pli-2 pbs-2'>
                    <Link href={'/apps/academy/course-details'} className='flex'>
                      <img src={item.tutorImg} alt={item.courseTitle} className='is-full' />
                    </Link>
                  </div>
                  <div className='flex flex-col gap-4 p-5'>
                    <div className='flex items-center justify-between'>
                      <Chip label={item.tags} variant='tonal' size='small' color={chipColor[item.tags].color} />
                      <div className='flex items-start'>
                        <Typography className='font-medium mie-1'>{item.rating}</Typography>
                        <i className='ri-star-fill text-warning mie-2' />
                        <Typography>{`(${item.ratingCount})`}</Typography>
                      </div>
                    </div>
                    <div className='flex flex-col gap-1'>
                      <Typography
                        variant='h5'
                        component={Link}
                        href={'/apps/academy/course-details'}
                        className='hover:text-primary'
                      >
                        {item.courseTitle}
                      </Typography>
                      <Typography>{item.desc}</Typography>
                    </div>
                    <div className='flex flex-col gap-1'>
                      {item.completedTasks === item.totalTasks ? (
                        <div className='flex items-center gap-1'>
                          <i className='ri-check-line text-xl text-success' />
                          <Typography color='success.main'>Completed</Typography>
                        </div>
                      ) : (
                        <div className='flex items-center gap-1'>
                          <i className='ri-time-line text-xl' />
                          <Typography>{`${item.time}`}</Typography>
                        </div>
                      )}
                      <LinearProgress
                        color='primary'
                        value={Math.floor((item.completedTasks / item.totalTasks) * 100)}
                        variant='determinate'
                        className='is-full bs-2'
                      />
                    </div>
                    {item.completedTasks === item.totalTasks ? (
                      <Button
                        variant='outlined'
                        startIcon={<i className='ri-refresh-line' />}
                        component={Link}
                        href={'/apps/academy/course-details'}
                      >
                        Start Over
                      </Button>
                    ) : (
                      <div className='flex flex-wrap gap-4'>
                        <Button
                          fullWidth
                          variant='outlined'
                          color='secondary'
                          startIcon={<i className='ri-refresh-line' />}
                          component={Link}
                          href={'/apps/academy/course-details'}
                          className='is-auto flex-auto'
                        >
                          Start Over
                        </Button>
                        <Button
                          fullWidth
                          variant='outlined'
                          endIcon={
                            <DirectionalIcon ltrIconClass='ri-arrow-right-line' rtlIconClass='ri-arrow-left-line' />
                          }
                          component={Link}
                          href={'/apps/academy/course-details'}
                          className='is-auto flex-auto'
                        >
                          Continue
                        </Button>
                      </div>
                    )}
                  </div>
                </div>
              </Grid>
            ))}
          </Grid>
        ) : (
          <Typography className='text-center'>No courses found</Typography>
        )}
        <div className='flex justify-center'>
          <Pagination
            count={Math.ceil(data.length / 6)}
            page={activePage + 1}
            showFirstButton
            showLastButton
            variant='tonal'
            color='primary'
            onChange={(e, page) => setActivePage(page - 1)}
          />
        </div>
      </CardContent>
    </Card>
  )
}

export default Courses
