// React Imports
import { useEffect, useRef } from 'react'

// MUI Imports
import Typography from '@mui/material/Typography'
import Grid from '@mui/material/Grid'
import MuiCard from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import { styled } from '@mui/material/styles'

// Type Imports
import type { ThemeColor } from '@core/types'

// Hook Imports
import { useIntersection } from '@/hooks/useIntersection'

// SVG Imports
import ElementOne from '@/assets/svg/front-pages/landing-page/ElementOne'
import Lines from '@assets/svg/front-pages/landing-page/Lines'

// Styles Imports
import frontCommonStyles from '@views/home/styles.module.css'

import styles from './styles.module.css'

// Data
const team = [
  {
    name: '王伟',
    position: '党委书记',
    image: '/images/front-pages/landing-page/sophie.png',
    color: 'var(--mui-palette-primary-mainOpacity)'
  },
  {
    name: '李华',
    position: '党委副书记、校长',
    image: '/images/front-pages/landing-page/nannie.png',
    color: 'var(--mui-palette-error-mainOpacity)'
  },
  {
    name: '张强',
    position: '党委委员、副校长、工会主席',
    image: '/images/front-pages/landing-page/chris.png',
    color: 'var(--mui-palette-success-mainOpacity)'
  },
  {
    name: '赵丽',
    position: '副校长',
    image: '/images/front-pages/landing-page/paul.png',
    color: 'var(--mui-palette-info-mainOpacity)'
  },
  {
    name: '陈刚',
    position: '教务处主任',
    image: '/images/front-pages/landing-page/paul.png',
    color: 'var(--mui-palette-info-mainOpacity)'
  }
]

const Card = styled(MuiCard)`
  &:hover {
    border-color: ${(props: { color: ThemeColor }) => props.color};

    & i:nth-child(1) {
      color: rgb(59, 89, 152) !important;
    }
    & i:nth-child(2) {
      color: rgb(0, 172, 238) !important;
    }
    & i:nth-child(3) {
      color: rgb(0, 119, 181) !important;
    }
  }
`

const OurTeam = () => {
  // Refs
  const skipIntersection = useRef(true)
  const ref = useRef<null | HTMLDivElement>(null)

  // Hooks
  const { updateIntersections } = useIntersection()

  useEffect(() => {
    const observer = new IntersectionObserver(
      ([entry]) => {
        if (skipIntersection.current) {
          skipIntersection.current = false

          return
        }

        updateIntersections({ [entry.target.id]: entry.isIntersecting })
      },
      { threshold: 0.35 }
    )

    ref.current && observer.observe(ref.current)
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [])

  
  const heroSectionBg = '/images/front-pages/landing-page/hero-bg-light.png'

  return (
    <section id='team' className='relative overflow-hidden pbs-[70px] -mbs-[70px] bg-backgroundPaper z-[1] plb-[50px]' ref={ref}>
      <img src={heroSectionBg} alt='hero-bg' className={styles.heroSectionBg} />
        <div className='flex flex-col items-center justify-center' style={{paddingTop: '50px'}}>
          <div className='flex is-full justify-center relative'>
            <ElementOne className='absolute inline-end-0' />
            <div className='flex items-center justify-center mbe-6 gap-3'>
              <Lines />
              <Typography color='text.primary' className='font-medium uppercase'>
                优秀的领导团队
              </Typography>
            </div>
          </div>
          <div className='flex items-center justify-center flex-wrap gap-2 mbe-3 sm:mbe-1'>
            <Typography variant='h4' className='font-bold'>
            以创新与责任为核心，推动教育质量提升，助力每位学生成长。
            </Typography>
          </div>
          <Typography color='text.secondary' className='font-medium text-center' sx={{mt: 5}}>
            我们的领导团队是一群充满激情的教育者，他们用智慧和关怀引领学校，确保每个学生都能茁壮成长。
          </Typography>
        </div>
        <Grid container rowSpacing={16} columnSpacing={6} className='mbe-8 pbs-[100px]'>
          {team.map((member, index) => (
            <Grid item xs={12} md={6} lg={3} key={index}>
              <Card className='shadow-none border overflow-visible' color={member.color as ThemeColor}>
                <CardContent className='flex flex-col items-center justify-center p-0'>
                  <div
                    className='flex justify-center is-full mli-auto text-center bs-[189px] relative overflow-visible rounded-ss-md rounded-se-md'
                    style={{ backgroundColor: member.color }}
                  >
                    <img src={member.image} alt={member.name} className='bs-[240px] absolute block-start-[-50px]' />
                  </div>
                  <div className='flex flex-col gap-3 p-5 is-full'>
                    <div className='text-center'>
                      <Typography variant='h5'>{member.name}</Typography>
                      <Typography color='text.secondary'>{member.position}</Typography>
                    </div>
                    <div className='flex gap-3 item-center justify-center'>
                      <i className='ri-facebook-circle-line text-[22px] text-textPrimary' />
                      <i className='ri-twitter-line text-[22px] text-textPrimary' />
                      <i className='ri-linkedin-box-line text-[22px] text-textPrimary' />
                    </div>
                  </div>
                </CardContent>
              </Card>
            </Grid>
          ))}
        </Grid>
    </section>
  )
}

export default OurTeam
