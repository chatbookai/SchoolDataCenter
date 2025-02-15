// MUI Imports
import Grid from '@mui/material/Grid'

// Component Imports
import WelcomeCard from '@/views/Dashboard/WelcomeCard'
import InterestedTopics from '@/views/Dashboard/InterestedTopics'
import PopularInstructors from '@/views/Dashboard/PopularInstructors'
import TopCourses from '@/views/Dashboard/TopCourses'
import UpcomingWebinar from '@/views/Dashboard/UpcomingWebinar'
import AssignmentProgress from '@/views/Dashboard/AssignmentProgress'
import CourseTable from '@/views/Dashboard/CourseTable'

// Data Imports
import { db as getAcademyData } from '@/views/Teacher/db'

/**
 * ! If you need data using an API call, uncomment the below API code, update the `process.env.API_URL` variable in the
 * ! `.env` file found at root of your project and also update the API endpoints like `/apps/academy` in below example.
 * ! Also, remove the above server action import and the action itself from the `src/app/server/actions.ts` file to clean up unused code
 * ! because we've used the server action for getting our static data.
 */

/* const getAcademyData = async () => {
  // Vars
  const res = await fetch(`${process.env.API_URL}/apps/academy`)

  if (!res.ok) {
    throw new Error('Failed to fetch academy data')
  }

  return res.json()
} */

import frontCommonStyles from '@views/home/styles.module.css'

const AcademyDashboard = async () => {
  // Vars
  const data = getAcademyData

  return (
    <section id='home' className='relative overflow-hidden pbs-[70px] -mbs-[70px] bg-backgroundPaper z-[1]'>
      <div className={frontCommonStyles.layoutSpacing} style={{paddingTop: '20px', paddingBottom: '20px'}}>
        <Grid container spacing={6}>
          <Grid item xs={12}>
            <WelcomeCard />
          </Grid>
          <Grid item xs={12} md={8}>
            <InterestedTopics />
          </Grid>
          <Grid item xs={12} sm={6} md={4}>
            <PopularInstructors />
          </Grid>
          <Grid item xs={12} sm={6} md={4}>
            <TopCourses />
          </Grid>
          <Grid item xs={12} sm={6} md={4}>
            <UpcomingWebinar />
          </Grid>
          <Grid item xs={12} sm={6} md={4}>
            <AssignmentProgress />
          </Grid>
          <Grid item xs={12}>
            <CourseTable courseData={data?.courses} />
          </Grid>
        </Grid>
      </div>
    </section>
  )
}

export default AcademyDashboard
