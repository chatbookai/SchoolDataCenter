// ** React Imports
import { useState, Fragment, useEffect } from 'react'

import Grid from '@mui/material/Grid'
import Box from '@mui/material/Box'
import CircularProgress from '@mui/material/CircularProgress'
import Typography from '@mui/material/Typography'

import { useAuth } from 'src/hooks/useAuth'

import Footer from '../Layout/Footer'
import MyProfile from '../Setting/MyProfile'
import Index from '../Index/Index'
import Login from '../Login/Login'

const Home = () => {
  const auth = useAuth()
  const logout = auth.logout
  const refresh = auth.refresh
  const user:any = auth.user

  const [currentTab, setCurrentTab] = useState<string>('Loading')
  const [disabledFooter, setDisabledFooter] = useState<boolean>(true)

  useEffect(() => {
    const refreshUserToken = async () => {
      try {
        if (user) {
          refresh(user)
          if(currentTab == "Login" || currentTab == "Loading")  {
            setCurrentTab("Index")
            setDisabledFooter(false)
          }
        }
        console.log("当前用户状态: ", user)
      } 
      catch (error) {
        console.error('Error fetching data:', error);
      }
    };
    
    refreshUserToken();

    const delay3SCheckUser = async () => {
      setTimeout(() => {
        if (!user) {
          setCurrentTab("Login")
        }
      }, 1000);
    }

    delay3SCheckUser();

    const intervalId = setInterval(refreshUserToken, 30000);

    return () => {
      clearInterval(intervalId);
    }

  }, []); 

  return (
    <Fragment>
      {currentTab == "Loading" && (
        <Grid item xs={12} sm={12} container justifyContent="space-around">
            <Box sx={{ mt: 60, mb: 6, display: 'flex', alignItems: 'center', flexDirection: 'column' }}>
                <CircularProgress 
                  sx={{
                    width: '60px !important',
                    height: '60px !important',
                  }}
                />
                <Typography sx={{mt: 10}}>{'用户身份校验中，请稍等'}</Typography>
            </Box>
        </Grid>
      )}
      {currentTab == "Login" && (<Login setCurrentTab={setCurrentTab} setDisabledFooter={setDisabledFooter} />)}
      {currentTab == "MyProfile" && (<MyProfile logout={logout} />)}
      {currentTab == "Index" && (<Index />)}
      <Footer Hidden={false} setCurrentTab={setCurrentTab} currentTab={currentTab} disabledFooter={disabledFooter} setDisabledFooter={setDisabledFooter} />
    </Fragment>
  )
}

export default Home
