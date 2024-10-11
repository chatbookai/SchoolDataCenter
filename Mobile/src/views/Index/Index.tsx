// ** React Imports
import { useState, useEffect, Fragment } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'
import Grid from '@mui/material/Grid'
import Typography from '@mui/material/Typography'
import Container from '@mui/material/Container'

// ** MUI Imports
//import Icon from '../../@core/components/icon'
import authConfig from '../../configs/auth'

import { styled } from '@mui/material/styles'
import Header from '../Layout/Header'

import axios from 'axios'
import { DecryptDataAES256GCM } from 'src/configs/functions'

import EngineeModelApp from "src/views/Enginee/index"


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

const Index = ({  }: any) => {
  // ** Hook
  const [menuArray, setMenuArray] = useState<any[]>([])

  const contentHeightFixed = {}
  const [counter, setCounter] = useState<number>(0)

  const [pageModel, setPageModel] = useState<string>('MainSetting')
  const [HeaderHidden, setHeaderHidden] = useState<boolean>(false)
  const [LeftIcon, setLeftIcon] = useState<string>('')
  const [Title, setTitle] = useState<string>('应用')
  const [RightButtonText, setRightButtonText] = useState<string>('')
  const [RightButtonIcon, setRightButtonIcon] = useState<string>('')
  const [appItemId, setAppItemId] = useState<string>('')

  useEffect(() => {
    handleGetMainMenus()
  }, []);

  const handleGetMainMenus = () => {
    if(window)  {
      const storageMainMenus = window.localStorage.getItem(authConfig.storageMainMenus)
      if(storageMainMenus && storageMainMenus != undefined) {
        try{
          const storageMainMenusJson = JSON.parse(storageMainMenus)
          setMenuArray(storageMainMenusJson)
        }
        catch(Error: any) {
            console.log("handleGetMainMenus storageMainMenus Error", storageMainMenus)
        }
      }
    }
    const backEndApi = authConfig.indexMenuspath
    const storedToken = window.localStorage.getItem(authConfig.storageTokenKeyName)!
    axios.get(authConfig.backEndApiHost + backEndApi, { headers: { Authorization: storedToken } }).then(res => {
      let dataJson: any = null
      const data = res.data
      if(data && data.isEncrypted == "1" && data.data)  {
          const i = data.data.slice(0, 32);
          const t = data.data.slice(-32);
          const e = data.data.slice(32, -32);
          const k = authConfig.k;
          const DecryptDataAES256GCMData = DecryptDataAES256GCM(e, i, t, k)
          try{
              dataJson = JSON.parse(DecryptDataAES256GCMData)
          }
          catch(Error: any) {
              console.log("handleGetMainMenus DecryptDataAES256GCMData view_default Error", Error)
              dataJson = data
          }
      }
      else {
          dataJson = data
      }
      if(dataJson) {
        setMenuArray(dataJson)
      }
      if(window && dataJson) {
        window.localStorage.setItem(authConfig.storageMainMenus, JSON.stringify(dataJson))
      }
      console.log("handleGetMainMenus menuArray dataJson", dataJson)
    })
    .catch(error => {
      if (error.response) {
        console.error('handleGetMainMenus Error response:', error.response.data);
        console.error('handleGetMainMenus Error status:', error.response.status);
        console.error('handleGetMainMenus Error headers:', error.response.headers);
      }
      else if (error.request) {
        console.error('handleGetMainMenus Error request:', error.request);
      }
      else {
        console.error('handleGetMainMenus Error message:', error.message);
      }
      console.error('handleGetMainMenus Error config:', error.config);
    });
  }

  const handleWalletGoHome = () => {
    setRefreshWalletData(refreshWalletData+1)
    setPageModel('MainSetting')
    setLeftIcon('')
    setTitle('应用')
    setRightButtonText('')
    setRightButtonIcon('')
  }

  const LeftIconOnClick = () => {
    switch(pageModel) {
      case 'MainSetting':
      case 'EngineeModelApp':
        handleWalletGoHome()
        break
    }
  }

  const RightButtonOnClick = () => {
    switch(pageModel) {
        case 'Contacts':
          break
      }
  }

  const [refreshWalletData, setRefreshWalletData] = useState<number>(0)

  useEffect(() => {
    setHeaderHidden(false)
    setRightButtonIcon('')
  }, []);

  const handleGoAppItem = (item: any) => {
    setAppItemId(item.path.replace('/apps/', ''))
    setCounter(counter + 1)
    setPageModel('EngineeModelApp')
    setLeftIcon('mdi:arrow-left-thin')
    setTitle(item.title)
    setRightButtonText('')
    setRightButtonIcon('')
  }

  return (
    <Fragment>
      <Header Hidden={HeaderHidden} LeftIcon={LeftIcon} LeftIconOnClick={LeftIconOnClick} Title={Title} RightButtonText={RightButtonText} RightButtonOnClick={RightButtonOnClick} RightButtonIcon={RightButtonIcon}/>

      <Box
        component="main"
        sx={{
          flex: 1,
          overflowY: 'auto',
          marginTop: '48px', // Adjust according to the height of the AppBar
          marginBottom: '56px', // Adjust according to the height of the Footer
        }}
      >
        <ContentWrapper
            className='layout-page-content'
            sx={{
                ...(contentHeightFixed && {
                overflow: 'hidden',
                '& > :first-of-type': { height: `calc(100% - 104px)` }
                })
            }}
            >

            {pageModel == 'MainSetting' && (
              <Container>
                {menuArray && menuArray.length > 0 && menuArray.map((menuItem: any, menuIndex: number)=>{

                  return (
                    <Box my={2} key={menuIndex}>
                        <Typography variant="h6" sx={{ py: 0.5, pl: 2, borderRadius: '5px', mb: 2, fontSize: '16px' }}>
                          {menuItem.title}
                        </Typography>
                      <Grid container spacing={2}>
                        {menuItem.children && menuItem.children.map((item: any, index: number) => (
                          <Grid item xs={3} key={index}>
                            <Box textAlign="center" sx={{my: 0}}>
                              <img src={authConfig.AppLogo} alt={item.title} style={{ width: '45px', height: '45px' }} onClick={()=>handleGoAppItem(item)}/>
                              <Typography variant="body2"
                                sx={{
                                  my: 0,
                                  whiteSpace: 'nowrap',
                                  overflow: 'hidden',
                                  textOverflow: 'ellipsis'
                                }}
                                onClick={()=>handleGoAppItem(item)}
                              >{item.title}</Typography>
                            </Box>
                          </Grid>
                        ))}
                      </Grid>
                    </Box>
                  )

                })}
              </Container>
            )}

            {pageModel == 'EngineeModelApp' && appItemId && (
              <>
                <EngineeModelApp backEndApi={`apps/apps_${appItemId}.php`} externalId=''/>
              </>
            )}

        </ContentWrapper>
      </Box>
    </Fragment>
  )
}

export default Index
