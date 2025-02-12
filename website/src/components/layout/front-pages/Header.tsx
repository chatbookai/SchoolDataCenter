'use client'

// React Imports
import { useState } from 'react'

// Next Imports
import Link from 'next/link'

// MUI Imports
import useScrollTrigger from '@mui/material/useScrollTrigger'

// Third-party Imports
import classnames from 'classnames'

// Type Imports
import type { Mode } from '@core/types'

// Component Imports
import Logo from '@components/layout/shared/Logo'
import ModeDropdown from '@components/layout/shared/ModeDropdown'
import FrontMenu from './FrontMenu'

// Util Imports
import { frontLayoutClasses } from '@layouts/utils/layoutClasses'

//import { useImageVariant } from '@core/hooks/useImageVariant'

// Styles Imports
import styles from '@components/layout/front-pages/styles.module.css'

const Header = ({ mode }: { mode: Mode }) => {
  // States
  const [isDrawerOpen, setIsDrawerOpen] = useState(false)

  // Detect window scroll
  const trigger = useScrollTrigger({
    threshold: 0,
    disableHysteresis: true
  })

  //const dropdownImageLight = '/images/website/index_header_top.png'
  //const dropdownImageDark = '/images/website/index_header_top.png'
  //const dropdownImage = useImageVariant(mode, dropdownImageLight, dropdownImageDark)
  //<img src={dropdownImage} className="w-full h-auto rounded-lg" />

  return (
    <header className={classnames(frontLayoutClasses.header, styles.header)}>
      <div className={classnames(frontLayoutClasses.navbar, styles.navbar, { [styles.headerScrolled]: trigger })}>        
        <div className={classnames(frontLayoutClasses.navbarContent, styles.navbarContent)}>
          <div className='flex items-center gap-4'>           
            <Logo />
            <FrontMenu mode={mode} isDrawerOpen={isDrawerOpen} setIsDrawerOpen={setIsDrawerOpen} />
          </div>
          <div className='flex items-center sm:gap-4'>
            <ModeDropdown />
          </div>
        </div>
      </div>
    </header>
  )
}

export default Header
