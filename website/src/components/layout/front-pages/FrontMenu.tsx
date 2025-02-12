'use client'

// React Imports
import { useEffect } from 'react'

// Next Imports
import { usePathname } from 'next/navigation'
import Link from 'next/link'

// MUI Imports
import Typography from '@mui/material/Typography'
import Drawer from '@mui/material/Drawer'
import useMediaQuery from '@mui/material/useMediaQuery'
import type { Theme } from '@mui/material/styles'
import IconButton from '@mui/material/IconButton'

// Third-party Imports
import classnames from 'classnames'

// Type Imports
import type { Mode } from '@core/types'

// Hook Imports
import { useIntersection } from '@/hooks/useIntersection'

// Component Imports
import FrontMenuDropDown from './FrontMenuDropDown'

type Props = {
  mode: Mode
  isDrawerOpen: boolean
  setIsDrawerOpen: (open: boolean) => void
}

type WrapperProps = {
  children: React.ReactNode
  isBelowLgScreen: boolean
  className?: string
  isDrawerOpen: boolean
  setIsDrawerOpen: (open: boolean) => void
}

const Wrapper = (props: WrapperProps) => {
  // Props
  const { children, isBelowLgScreen, className, isDrawerOpen, setIsDrawerOpen } = props

  if (isBelowLgScreen) {
    return (
      <Drawer
        variant='temporary'
        anchor='left'
        open={isDrawerOpen}
        onClose={() => setIsDrawerOpen(false)}
        ModalProps={{
          keepMounted: true
        }}
        sx={{ '& .MuiDrawer-paper': { width: ['100%', 300] } }}
        className={classnames('p-5', className)}
      >
        <div className='p-4 flex flex-col gap-x-3'>
          <IconButton onClick={() => setIsDrawerOpen(false)} className='absolute inline-end-4 block-start-2'>
            <i className='ri-close-line' />
          </IconButton>
          {children}
        </div>
      </Drawer>
    )
  }

  return <div className={classnames('flex items-center flex-wrap gap-x-4 gap-y-3', className)}>{children}</div>
}

const FrontMenu = (props: Props) => {
  // Props
  const { isDrawerOpen, setIsDrawerOpen, mode } = props

  // Hooks
  const pathname = usePathname()
  const isBelowLgScreen = useMediaQuery((theme: Theme) => theme.breakpoints.down('lg'))
  const { intersections } = useIntersection()
  console.log("intersections", intersections, mode)

  useEffect(() => {
    if (!isBelowLgScreen && isDrawerOpen) {
      setIsDrawerOpen(false)
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [isBelowLgScreen])

  const HeaderMenusList = [
    {title:'首页', target:'', href:'/home', default: true},
    {title:'学校概况', target:'', href:'', default: false, children:
        [
          {title: '学校简介', target:'', href: '/pricing' },
          {title: '学校领导', target:'', href: '/pricing' },
          {title: '学校风采', target:'', href: '/pricing' },
          {title: '组织机构', target:'', href: '/pricing' },
        ]
    },
    {title:'学校新闻', target:'', href:'', default: false, children:
      [
        {title: '新闻咨询', target:'', href: '/pricing' },
        {title: '校园公告', target:'', href: '/pricing' },
      ]
    },
    {title:'教育教学', target:'', href:'', default: false, children:
      [
        {title: '教学管理', target:'', href: '/pricing' },
        {title: '教学改革', target:'', href: '/pricing' },
        {title: '人陪方案', target:'', href: '/pricing' },
        {title: '教学成果奖', target:'', href: '/pricing' },
      ]
    },
    {title:'校园风采', target:'', href:'', default: false, children:
      [
        {title: '学生风采', target:'', href: '/pricing' },
        {title: '老师风采', target:'', href: '/pricing' },
        {title: '优秀老师', target:'', href: '/pricing' },
        {title: '优秀班主任', target:'', href: '/pricing' },
        {title: '优秀学生', target:'', href: '/pricing' },
      ]
    },
    {title:'学生资助', target:'', href:'', default: false, children:
      [
        {title: '资助政策', target:'', href: '/pricing' },
        {title: '资助公告', target:'', href: '/pricing' },
        {title: '资助动态', target:'', href: '/pricing' },
      ]
    },
    {title:'党团工会', target:'', href:'', default: false, children:
      [
        {title: '职工之家', target:'', href: '/pricing' },
        {title: '党建工作', target:'', href: '/pricing' },
      ]
    },
    {title:'招生就业', target:'', href:'', default: false, children:
      [
        {title: '招生信息', target:'', href: '/pricing' },
        {title: '就业信息', target:'', href: '/pricing' },
        {title: '在线报名', target:'', href: '/pricing' },
      ]
    },
    {title:'校友之家', target:'', href:'', default: false, children:
      [
        {title: '校友动态', target:'', href: '/pricing' },
        {title: '校友联络', target:'', href: '/pricing' },
        {title: '校友风采', target:'', href: '/pricing' },
        {title: '流金岁月', target:'', href: '/pricing' },
      ]
    },
  ]

  return (
    <Wrapper isBelowLgScreen={isBelowLgScreen} isDrawerOpen={isDrawerOpen} setIsDrawerOpen={setIsDrawerOpen}>
      {HeaderMenusList && HeaderMenusList.map((Item: any, Index: number)=>{

        if(Item.children && Item.children.length > 0)  {
          return (
            <FrontMenuDropDown key={Index}
              mode={mode}
              isBelowLgScreen={isBelowLgScreen}
              isDrawerOpen={isDrawerOpen}
              setIsDrawerOpen={setIsDrawerOpen}
              MenuInfor={Item}
            />
          )
        }
        else {
          return (
            <Typography key={Index}
              component={Link}
              href={Item.target}
              className={classnames('font-medium hover:text-primary', { 'text-primary': Item.default && pathname === '/home' })}
              color='text.primary'
            >
              {Item.title}
            </Typography>
          )
        }
      })}
    </Wrapper>
  )
}

export default FrontMenu
