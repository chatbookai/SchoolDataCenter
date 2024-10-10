import { useState } from 'react';

// ** MUI Imports
import Box from '@mui/material/Box'

// ** Type Import
import { LayoutProps } from '../../../../@core/layouts/types'

import Icon from '../../../../@core/components/icon'

import BottomNavigation from '@mui/material/BottomNavigation';
import BottomNavigationAction from '@mui/material/BottomNavigationAction';
import { useTranslation } from 'react-i18next'


interface Props {
  settings: LayoutProps['settings']
  saveSettings: LayoutProps['saveSettings']
  footerStyles?: NonNullable<LayoutProps['footerProps']>['sx']
  footerContent?: NonNullable<LayoutProps['footerProps']>['content']
}

const Footer = (props: Props) => {
  // ** Props
  const { settings } = props
  const { t } = useTranslation()

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
