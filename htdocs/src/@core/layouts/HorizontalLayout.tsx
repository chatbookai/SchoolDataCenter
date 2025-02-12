// ** MUI Imports
import Fab from '@mui/material/Fab'
import AppBar from '@mui/material/AppBar'
import Box, { BoxProps } from '@mui/material/Box'
import Typography from '@mui/material/Typography'
import { styled, useTheme } from '@mui/material/styles'
import MuiToolbar, { ToolbarProps } from '@mui/material/Toolbar'

// ** Next Import
import Link from 'next/link'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Theme Config Import
import themeConfig from 'src/configs/themeConfig'

// ** Type Import
import { LayoutProps } from 'src/@core/layouts/types'

// ** Components
import Customizer from 'src/@core/components/customizer'
import Footer from './components/shared-components/footer'
import Navigation from './components/horizontal/navigation'
import ScrollToTop from 'src/@core/components/scroll-to-top'


// ** Util Import
import { hexToRGBA } from 'src/@core/utils/hex-to-rgba'

const HorizontalLayoutWrapper = styled('div')({
  height: '100%',
  display: 'flex',
  ...(themeConfig.horizontalMenuAnimation && { overflow: 'clip' })
})

const MainContentWrapper = styled(Box)<BoxProps>({
  flexGrow: 1,
  minWidth: 0,
  display: 'flex',
  minHeight: '100vh',
  flexDirection: 'column'
})

const Toolbar = styled(MuiToolbar)<ToolbarProps>(({ theme }) => ({
  width: '100%',
  padding: `${theme.spacing(0, 6)} !important`,
  [theme.breakpoints.down('sm')]: {
    paddingLeft: theme.spacing(2),
    paddingRight: theme.spacing(4)
  },
  [theme.breakpoints.down('xs')]: {
    paddingLeft: theme.spacing(2),
    paddingRight: theme.spacing(2)
  }
}))

const ContentWrapper = styled('main')(({ theme }) => ({
  flexGrow: 1,
  width: '100%',
  padding: theme.spacing(6),
  transition: 'padding .25s ease-in-out',
  [theme.breakpoints.down('sm')]: {
    paddingLeft: theme.spacing(4),
    paddingRight: theme.spacing(4)
  }
}))

const HorizontalLayout = (props: LayoutProps) => {
  // ** Props
  const {
    hidden,
    children,
    settings,
    scrollToTop,
    footerProps,
    contentHeightFixed,
    horizontalLayoutProps
  } = props

  // ** Vars
  const { skin, appBar, navHidden, appBarBlur, contentWidth } = settings
  const appBarProps = horizontalLayoutProps?.appBar?.componentProps
  const userNavMenuContent = horizontalLayoutProps?.navMenu?.content

  const theme = useTheme()

  console.log("horizontalLayoutProps", horizontalLayoutProps)

  let userAppBarStyle = {}
  if (appBarProps && appBarProps.sx) {
    userAppBarStyle = appBarProps.sx
  }
  const userAppBarProps = Object.assign({}, appBarProps)
  delete userAppBarProps.sx

  const StyledLink = styled(Link)(({ theme }) => ({
    display: 'flex',
    alignItems: 'center',
    textDecoration: 'none',
    marginRight: theme.spacing(8)
  }))

  return (
    <HorizontalLayoutWrapper className='layout-wrapper'>
      <MainContentWrapper className='layout-content-wrapper' sx={{ ...(contentHeightFixed && { maxHeight: '100vh' }) }}>
        {/* Navbar (or AppBar) and Navigation Menu Wrapper */}
        <AppBar
          color='default'
          elevation={skin === 'bordered' ? 0 : 3}
          className='layout-navbar-and-nav-container'
          position={appBar === 'fixed' ? 'sticky' : 'static'}
          sx={{
            alignItems: 'center',
            color: 'text.primary',
            justifyContent: 'center',
            backgroundColor: 'background.paper',
            ...(appBar === 'static' && { zIndex: 13 }),
            ...(skin === 'bordered' && { borderBottom: theme => `1px solid ${theme.palette.divider}` }),
            transition: 'border-bottom 0.2s ease-in-out, backdrop-filter .25s ease-in-out, box-shadow .25s ease-in-out',
            ...(appBar === 'fixed'
              ? appBarBlur && {
                  backdropFilter: 'blur(8px)',
                  backgroundColor: theme => hexToRGBA(theme.palette.background.paper, 0.85)
                }
              : {}),
            ...userAppBarStyle
          }}
          {...userAppBarProps}
        >
          {/* Navbar / AppBar */}
          <Box
            className='layout-navbar'
            sx={{
              width: '100%',
              ...(navHidden ? {} : { borderBottom: theme => `1px solid ${theme.palette.divider}` })
            }}
          >
            <Toolbar
              className='navbar-content-container'
              sx={{
                mx: 'auto',
                ...(contentWidth === 'boxed' && { '@media (min-width:1920px)': { maxWidth: 1920 } }),
                minHeight: theme => `${(theme.mixins.toolbar.minHeight as number) - 1}px !important`
              }}
            >
              <StyledLink href='/'>
                <svg
                  width={30}
                  height={25}
                  version='1.1'
                  viewBox='0 0 30 23'
                  xmlns='http://www.w3.org/2000/svg'
                  xmlnsXlink='http://www.w3.org/1999/xlink'
                >
                  <g stroke='none' strokeWidth='1' fill='none' fillRule='evenodd'>
                    <g id='Artboard' transform='translate(-95.000000, -51.000000)'>
                      <g id='logo' transform='translate(95.000000, 50.000000)'>
                        <path
                          id='Combined-Shape'
                          fill={theme.palette.primary.main}
                          d='M30,21.3918362 C30,21.7535219 29.9019196,22.1084381 29.7162004,22.4188007 C29.1490236,23.366632 27.9208668,23.6752135 26.9730355,23.1080366 L26.9730355,23.1080366 L23.714971,21.1584295 C23.1114106,20.7972624 22.7419355,20.1455972 22.7419355,19.4422291 L22.7419355,19.4422291 L22.741,12.7425689 L15,17.1774194 L7.258,12.7425689 L7.25806452,19.4422291 C7.25806452,20.1455972 6.88858935,20.7972624 6.28502902,21.1584295 L3.0269645,23.1080366 C2.07913318,23.6752135 0.850976404,23.366632 0.283799571,22.4188007 C0.0980803893,22.1084381 2.0190442e-15,21.7535219 0,21.3918362 L0,3.58469444 L0.00548573643,3.43543209 L0.00548573643,3.43543209 L0,3.5715689 C3.0881846e-16,2.4669994 0.8954305,1.5715689 2,1.5715689 C2.36889529,1.5715689 2.73060353,1.67359571 3.04512412,1.86636639 L15,9.19354839 L26.9548759,1.86636639 C27.2693965,1.67359571 27.6311047,1.5715689 28,1.5715689 C29.1045695,1.5715689 30,2.4669994 30,3.5715689 L30,3.5715689 Z'
                        />
                        <polygon
                          id='Rectangle'
                          opacity='0.077704'
                          fill={theme.palette.common.black}
                          points='0 8.58870968 7.25806452 12.7505183 7.25806452 16.8305646'
                        />
                        <polygon
                          id='Rectangle'
                          opacity='0.077704'
                          fill={theme.palette.common.black}
                          points='0 8.58870968 7.25806452 12.6445567 7.25806452 15.1370162'
                        />
                        <polygon
                          id='Rectangle'
                          opacity='0.077704'
                          fill={theme.palette.common.black}
                          points='22.7419355 8.58870968 30 12.7417372 30 16.9537453'
                          transform='translate(26.370968, 12.771227) scale(-1, 1) translate(-26.370968, -12.771227) '
                        />
                        <polygon
                          id='Rectangle'
                          opacity='0.077704'
                          fill={theme.palette.common.black}
                          points='22.7419355 8.58870968 30 12.6409734 30 15.2601969'
                          transform='translate(26.370968, 11.924453) scale(-1, 1) translate(-26.370968, -11.924453) '
                        />
                        <path
                          id='Rectangle'
                          fillOpacity='0.15'
                          fill={theme.palette.common.white}
                          d='M3.04512412,1.86636639 L15,9.19354839 L15,9.19354839 L15,17.1774194 L0,8.58649679 L0,3.5715689 C3.0881846e-16,2.4669994 0.8954305,1.5715689 2,1.5715689 C2.36889529,1.5715689 2.73060353,1.67359571 3.04512412,1.86636639 Z'
                        />
                        <path
                          id='Rectangle'
                          fillOpacity='0.35'
                          fill={theme.palette.common.white}
                          transform='translate(22.500000, 8.588710) scale(-1, 1) translate(-22.500000, -8.588710) '
                          d='M18.0451241,1.86636639 L30,9.19354839 L30,9.19354839 L30,17.1774194 L15,8.58649679 L15,3.5715689 C15,2.4669994 15.8954305,1.5715689 17,1.5715689 C17.3688953,1.5715689 17.7306035,1.67359571 18.0451241,1.86636639 Z'
                        />
                      </g>
                    </g>
                  </g>
                </svg>
                <Typography
                  variant='h6'
                  sx={{
                    ml: 3,
                    fontWeight: 600,
                    lineHeight: 'normal'
                  }}
                >
                  {themeConfig.templateName}
                </Typography>
              </StyledLink>
              {navHidden ? null : (
                <Box className='layout-horizontal-nav' sx={{ width: '100%', ...horizontalLayoutProps?.navMenu?.sx }}>
                  <Toolbar
                    className='horizontal-nav-content-container'
                    sx={{
                      mx: 'auto',
                      ...(contentWidth === 'boxed' && { '@media (min-width:1920px)': { maxWidth: 1920 } }),
                      minHeight: theme =>
                        `${(theme.mixins.toolbar.minHeight as number) - (skin === 'bordered' ? 1 : 0)}px !important`
                    }}
                  >
                    {(userNavMenuContent && userNavMenuContent(props)) || (
                      <Navigation
                        {...props}
                        horizontalNavItems={
                          (horizontalLayoutProps as NonNullable<LayoutProps['horizontalLayoutProps']>)?.navMenu?.navItems
                        }
                      />
                    )}
                  </Toolbar>
                </Box>
              )}
            </Toolbar>
            
          </Box>

        </AppBar>

        {/* Content */}
        <ContentWrapper
          className='layout-page-content'
          sx={{
            ...(contentHeightFixed && { display: 'flex', overflow: 'hidden' }),
            ...(contentWidth === 'boxed' && {
              mx: 'auto',
              '@media (min-width:1920px)': { maxWidth: 1920 },
              '@media (min-width:1440px)': { maxWidth: '100%' },
              '@media (min-width:1200px)': { maxWidth: '100%' }
            })
          }}
        >
          {children}
        </ContentWrapper>

        {/* Footer */}
        <Footer {...props} footerStyles={footerProps?.sx} footerContent={footerProps?.content} />

        {/* Customizer */}
        {themeConfig.disableCustomizer || hidden ? null : <Customizer />}

        {/* Scroll to top button */}
        {scrollToTop ? (
          scrollToTop(props)
        ) : (
          <ScrollToTop className='mui-fixed'>
            <Fab color='primary' size='small' aria-label='scroll back to top'>
              <Icon icon='mdi:arrow-up' />
            </Fab>
          </ScrollToTop>
        )}
      </MainContentWrapper>
    </HorizontalLayoutWrapper>
  )
}

export default HorizontalLayout
