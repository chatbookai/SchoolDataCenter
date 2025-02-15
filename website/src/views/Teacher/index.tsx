'use client'

// React Imports
import { useState } from 'react'

// MUI Imports
import Grid from '@mui/material/Grid'

// Type Imports
import type { Mode } from '@core/types'
import type { Course } from '@/types/apps/academyTypes'

// Component Imports
import MyCourseHeader from './MyCourseHeader'
import Courses from './Courses'
import ColoredCards from './ColoredCards'
import FreeCourses from './FreeCourses'

type Props = {
  courseData?: Course[]
  mode: Mode
}

import frontCommonStyles from '@views/home/styles.module.css'

const AcademyMyCourse = ({ courseData, mode }: Props) => {
  // States
  const [searchValue, setSearchValue] = useState('')

  return (
    <section id='home' className='relative overflow-hidden pbs-[70px] -mbs-[70px] bg-backgroundPaper z-[1]'>
      <div className={frontCommonStyles.layoutSpacing} style={{paddingTop: '20px', paddingBottom: '20px'}}>
        <Grid container spacing={6}>
          <Grid item xs={12}>
            <MyCourseHeader mode={mode} searchValue={searchValue} setSearchValue={setSearchValue} />
          </Grid>
          <Grid item xs={12}>
            <Courses courseData={courseData} searchValue={searchValue} />
          </Grid>
          <Grid item xs={12}>
            <ColoredCards />
          </Grid>
          <Grid item xs={12}>
            <FreeCourses />
          </Grid>
        </Grid>
      </div>
    </section>
  )
}

export default AcademyMyCourse