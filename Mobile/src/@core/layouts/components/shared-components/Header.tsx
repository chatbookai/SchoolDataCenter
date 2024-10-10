// ** MUI Imports
import Icon from '../../../../@core/components/icon'

// ** Type Import
import { LayoutProps } from '../../../../@core/layouts/types'

import { AppBar, Toolbar, Typography, IconButton, Button } from '@mui/material';

interface Props {
  settings: LayoutProps['settings']
  saveSettings: LayoutProps['saveSettings']
  footerStyles?: NonNullable<LayoutProps['footerProps']>['sx']
  footerContent?: NonNullable<LayoutProps['footerProps']>['content']
}


const Header = (props: Props) => {
  // ** Props
  const { settings } = props

  // ** Vars
  const { skin, footer } = settings

  if (footer === 'hidden') {
    return null
  }

  return (
    <AppBar
      color='default'
      position='sticky'
      elevation={skin === 'bordered' ? 0 : 3}
      sx={{
        backgroundColor: 'background.paper',
      }}
    >
      <Toolbar sx={{ height: '100%', display: 'flex', alignItems: 'center', 'min-height': 48 }}>
        <IconButton size="small" edge="start" color="inherit" aria-label="menu">
          <Icon icon='material-symbols:swap-horiz-rounded' />
        </IconButton>
        <Typography variant="h6" component="div" sx={{ flexGrow: 1, textAlign: 'center' }}>
          {'我的'}
        </Typography>
        <Button size="small" color="inherit">{'Edit'}</Button>
      </Toolbar>
    </AppBar>
  )
}

export default Header
