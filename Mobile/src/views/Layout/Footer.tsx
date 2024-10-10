import { useState } from 'react';

// ** MUI Imports
import Box from '@mui/material/Box'

import Icon from '../../@core/components/icon'

import BottomNavigation from '@mui/material/BottomNavigation';
import BottomNavigationAction from '@mui/material/BottomNavigationAction';
import { useTranslation } from 'react-i18next'


const Footer = (props: any) => {
  // ** Props
  const { footer, setCurrentTab, disabledFooter } = props
  const { t } = useTranslation()

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
                setCurrentTab('首页')
                break;
              case 1:
                setCurrentTab('课表')
                break;
              case 2:
                setCurrentTab('应用')
                break;
              case 3:
                setCurrentTab('消息')
                break;
              case 3:
                setCurrentTab('联系人')
                break;
            }
          }}
          sx={{width: '100%'}}
        >
          <BottomNavigationAction label={t("首页")} disabled={disabledFooter} icon={<Icon icon='material-symbols:account-balance-wallet-outline' />} />
          <BottomNavigationAction label={t("课表")} disabled={disabledFooter} icon={<Icon icon='material-symbols:swap-horiz-rounded' />} />
          <BottomNavigationAction label={t("应用")} disabled={disabledFooter} icon={<Icon icon='icon-park-outline:all-application' />} />
          <BottomNavigationAction label={t("消息")} disabled={disabledFooter} icon={<Icon icon='mdi:email-outline' />} />
          <BottomNavigationAction label={t("联系人")} disabled={disabledFooter} icon={<Icon icon='material-symbols:settings-outline' />} />
        </BottomNavigation>
      </Box>
  )
}


export default Footer
