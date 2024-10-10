import { useState } from 'react';

// ** MUI Imports
import Box from '@mui/material/Box'

// ** Type Import
import { LayoutProps } from '../../../../@core/layouts/types'

import Icon from '../../../../@core/components/icon'

import BottomNavigation from '@mui/material/BottomNavigation';
import BottomNavigationAction from '@mui/material/BottomNavigationAction';

interface Props {
  settings: LayoutProps['settings']
  saveSettings: LayoutProps['saveSettings']
  footerStyles?: NonNullable<LayoutProps['footerProps']>['sx']
  footerContent?: NonNullable<LayoutProps['footerProps']>['content']
}

const Footer = (props: Props) => {
  // ** Props
  const { settings } = props

  const [value, setValue] = useState(0);

  // ** Vars
  const { footer } = settings

  if (footer === 'hidden') {
    return null
  }

  return (
      <Box
        component='footer'
        sx={{
          width: '100%',
          zIndex: 10,
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'center',
          bottom: 0,
          position: 'sticky',
        }}
      >
        <BottomNavigation
          showLabels
          value={value}
          onChange={(event, newValue) => {
            setValue(newValue);
            console.log("FooterContent event", event)
          }}
          sx={{width: '100%'}}
        >
          <BottomNavigationAction label={"首页"} icon={<Icon icon='material-symbols:home-work-outline' />} />
          <BottomNavigationAction label={"课表"} icon={<Icon icon='uil:schedule' />} />
          <BottomNavigationAction label={"应用"} icon={<Icon icon='icon-park-outline:all-application' />} />
          <BottomNavigationAction label={"消息"} icon={<Icon icon='mdi:message-processing-outline' />} />
          <BottomNavigationAction label={"我的"} icon={<Icon icon='mdi:account-box-outline' />} />
        </BottomNavigation>
      </Box>
  )
}

export default Footer
