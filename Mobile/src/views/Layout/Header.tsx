// ** MUI Imports
import Icon from '../../@core/components/icon'

import { AppBar, Toolbar, Typography, IconButton, Button, Box } from '@mui/material';

const Header = (props: any) => {
  // ** Props
  const { Hidden, LeftIcon, LeftIconOnClick, Title, RightButtonText, RightButtonOnClick, RightButtonIcon } = props

  if (Hidden == true) {
    return null
  }

  return (
    <AppBar
      color='default'
      position='fixed'
      elevation={0}
      sx={{
        backgroundColor: 'background.paper',
      }}
    >
      <Toolbar sx={{ height: '100%', display: 'flex', alignItems: 'center', minHeight: 48 }}>
        <Box width='100px'>
          {LeftIcon && LeftIconOnClick && (
            <IconButton size="small" edge="start" color="inherit" aria-label="menu" onClick={ () => LeftIconOnClick && LeftIconOnClick()}>
              <Icon icon={LeftIcon}/>
            </IconButton>
          )}
        </Box>
        <Typography variant="h6" component="div" sx={{ flexGrow: 1, textAlign: 'center' }}>
          {Title}
        </Typography>
        <Box display="flex" justifyContent="flex-end" width="100px">
          {RightButtonText && RightButtonIcon == null && RightButtonOnClick && (
            <Button size="small" color="inherit" onClick={ () => RightButtonOnClick && RightButtonOnClick()}>
              {RightButtonText}
            </Button>
          )}
          {RightButtonIcon && RightButtonOnClick && (
            <IconButton size="small" edge="start" color="inherit" aria-label="menu"  onClick={ () => RightButtonOnClick && RightButtonOnClick()}>
              <Icon icon={RightButtonIcon}/>
            </IconButton>
          )}
        </Box>
      </Toolbar>
    </AppBar>
  )
}

export default Header
