// React Imports
import { useEffect, useRef, useState } from 'react'

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

import CardActions from '@mui/material/CardActions'
import IconButton from '@mui/material/IconButton'
import Collapse from '@mui/material/Collapse'
import Divider from '@mui/material/Divider'

// Data
const team = [
  {
    name: '王伟',
    position: '党委书记',
    image: '/images/front-pages/landing-page/sophie.png',
    color: 'var(--mui-palette-primary-mainOpacity)',
    resume: `王伟，男，1975年生，中共党员，研究生学历。曾在XX市教育局工作多年，后担任XX中等职业学校副校长，并于2018年起担任该校党委书记，全面负责学校党的建设、组织工作及发展规划。王书记长期致力于职业教育改革，推动校企合作，促进学校高质量发展。`
  },
  {
    name: '李华',
    position: '党委副书记、校长',
    image: '/images/front-pages/landing-page/nannie.png',
    color: 'var(--mui-palette-error-mainOpacity)',
    resume: `李华，女，1978年生，中共党员，教育管理专业硕士。曾任XX职业技术学院教务处主任、副校长，2020年起担任XX中等职业学校校长，分管学校整体行政管理、教学改革及师资建设。李校长注重现代化职业教育体系建设，推动学校在技能培养和实习就业方面取得显著成效。`
  },
  {
    name: '张强',
    position: '党委委员、副校长、工会主席',
    image: '/images/front-pages/landing-page/chris.png',
    color: 'var(--mui-palette-success-mainOpacity)',
    resume: `张强，男，1980年生，中共党员，高级讲师。曾任XX职业学校实训中心主任、教务处副主任，2022年起担任XX中职学校副校长兼工会主席，主管学校工会事务、教职工福利及校园文化建设。张副校长致力于优化师资队伍，提高教学质量，推动学校文化建设。`
  },
  {
    name: '赵丽',
    position: '副校长',
    image: '/images/front-pages/landing-page/paul.png',
    color: 'var(--mui-palette-info-mainOpacity)',
    resume: `赵丽，女，1982年生，中共党员，职业教育管理专家。曾在XX省教育厅职业教育部门任职，2023年起担任XX中职学校副校长，主要负责学校招生就业工作、校企合作及学生管理事务。她积极推动学校与企业深度合作，构建产教融合体系，提升学生就业竞争力。`
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

  const teamExpanedList = Array.from({ length: team.length }, () => false);
  
  const [expanded, setExpanded] = useState<boolean[]>(teamExpanedList)

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

  
  
  
  
  return (
    <section id='team' className='plb-[50px]' ref={ref}>
      <div className={frontCommonStyles.layoutSpacing}>
        <div className='flex flex-col items-center justify-center'>
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
                  </div>
                </CardContent>
                <CardActions className='justify-between card-actions-dense'>
                  <Typography color='text.secondary' sx={{mt: 0}}></Typography>
                  <IconButton onClick={() => setExpanded(prev => {
                                        const newExpanded = [...prev];
                                        newExpanded[index] = !prev[index];
                                        console.log("newExpanded", newExpanded)
                                        
                                        return newExpanded;
                                      })}>
                    <i className={expanded[index] ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'} />
                  </IconButton>
                </CardActions>
                <Collapse in={expanded[index]} timeout={300}>
                  <Divider />
                  <CardContent>
                    <Typography dangerouslySetInnerHTML={{ __html: member.resume }} />
                  </CardContent>
                </Collapse>
              </Card>
            </Grid>
          ))}
        </Grid>
      </div>
    </section>
  )
}

export default OurTeam
