import { useState } from 'react';

// ** MUI Imports
import Box from '@mui/material/Box'

import Icon from '../../@core/components/icon'

import BottomNavigation from '@mui/material/BottomNavigation';
import BottomNavigationAction from '@mui/material/BottomNavigationAction';

import authConfig from 'src/configs/auth'

const Footer = (props: any) => {
  // ** Props
  const { footer, setCurrentTab, disabledFooter } = props

  const [value, setValue] = useState(0);

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
          position: 'fixed',
          overflow: 'visible', 
        }}
      >
        <BottomNavigation
          showLabels
          value={value}
          onChange={(event, newValue) => {
            setValue(newValue);
            console.log("FooterContent event", newValue)
            switch(newValue) {
              case 0:
                setCurrentTab('Index')
                break;
              case 1:
                setCurrentTab('Schedule')
                break;
              case 2:
                setCurrentTab('Application')
                break;
              case 3:
                setCurrentTab('Message')
                break;
              case 4:
                setCurrentTab('MyProfile')
                break;
            }
          }}
          sx={{width: '100%'}}
        >
          <BottomNavigationAction label={"首页"} disabled={disabledFooter} icon={<Icon icon='material-symbols:home-work-outline' />} />
          <BottomNavigationAction label={"课表"} disabled={disabledFooter} icon={<Icon icon='uil:schedule' />} />
          <BottomNavigationAction
            label={"应用"}
            disabled={disabledFooter}
            icon={
              <img
                src={authConfig.AppLogo}
                alt='应用'
                style={{
                  width: '3.5rem', // 控制图片的宽度
                  height: '3.5rem', // 控制图片的高度
                  objectFit: 'cover', // 确保图片按比例缩放并覆盖整个区域
                }}
              />
            }
            sx={{
              position: 'relative', 
              bottom: '1rem',
            }}
          />
          <BottomNavigationAction label={"消息"} disabled={disabledFooter} icon={<Icon icon='mdi:message-processing-outline' />} />
          <BottomNavigationAction label={"我的"} disabled={disabledFooter} icon={<Icon icon='mdi:account-box-outline' />} />
        </BottomNavigation>
      </Box>
  )
}


export default Footer
