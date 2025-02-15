'use client'
import { useState, useEffect } from 'react'

// MUI Imports
import Typography from '@mui/material/Typography'
import Button from '@mui/material/Button'
import Link from 'next/link'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import Grid from '@mui/material/Grid'

// Third-party Imports
import classnames from 'classnames'

// Styles Imports
import styles from './styles.module.css'
import frontCommonStyles from '@views/home/styles.module.css'

import OurTeam from './OurTeam'

const Leader = () => {
    const heroSectionBg = '/images/front-pages/landing-page/hero-bg-light.png'

    return (
        <section id='home' className='relative overflow-hidden pbs-[70px] -mbs-[70px] bg-backgroundPaper z-[1]'>
            <img src={heroSectionBg} alt='hero-bg' className={styles.heroSectionBg} />
            <OurTeam />
        </section>
    )
}

export default Leader
