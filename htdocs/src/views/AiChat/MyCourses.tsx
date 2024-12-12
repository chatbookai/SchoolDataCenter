// ** React Imports
import { useState, useEffect, Fragment } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import Grid from '@mui/material/Grid'
import Typography from '@mui/material/Typography'

import CircularProgress from '@mui/material/CircularProgress'

// ** Third Party Components
import axios from 'axios'

import { defaultConfig } from 'src/configs/auth'

import Header from '../Home/Header'
import { styled } from '@mui/material/styles'
import { useTranslation } from 'react-i18next'

import { DecryptDataAES256GCM } from 'src/configs/functions'
import ChatIndex from 'src/views/AiChat/ChatIndex'

const ContentWrapper = styled('main')(({ theme }) => ({
  flexGrow: 1,
  width: '100%',
  padding: theme.spacing(4),
  transition: 'padding .25s ease-in-out',
  [theme.breakpoints.down('sm')]: {
    paddingLeft: theme.spacing(4),
    paddingRight: theme.spacing(4)
  }
}))

const AllAiApp = ({authConfig}: any) => {
  const { t } = useTranslation()
  const [isLoading, setIsLoading] = useState<boolean>(true)
  const [myCoursesList, setMyCoursesList] = useState<any[]>([])
  const [chatApp, setChatApp] = useState<any[]>([])
  const [pageModel, setPageModel] = useState<string>('Main')
  const [app, setApp] = useState<any>(null)

  const [HeaderHidden, setHeaderHidden] = useState<boolean>(false)
  const [LeftIcon, setLeftIcon] = useState<string>('')
  const [Title, setTitle] = useState<string>(t('Course') as string)
  const [RightButtonText, setRightButtonText] = useState<string>('')
  const [RightButtonIcon, setRightButtonIcon] = useState<string>('')
  const [historyCounter, setHistoryCounter] = useState<number>(0)
  const [clearButtonClickEvent, setClearButtonClickEvent] = useState<boolean>(false)

  const QuestionGuideTemplate = '你是一个AI智能助手，可以回答和解决我的问题。请结合前面的对话记录，帮我生成 3 个问题，引导我继续提问。问题的长度应小于20个字符，要求使用UTF-8编码，按 JSON 格式返回: ["问题1", "问题2", "问题3"]'

  const handleSetChatWithApp = async (item: any) => {
    if(false) {
      setLeftIcon('ic:twotone-keyboard-arrow-left')
      setPageModel("ChatWithApp")
      setTitle(item.AppName)
      setApp({...item, id: "ChatApp-" + item.id, AppName2: item.AppModel, avatar: '1.png', Model: {}, QuestionGuideTemplate })
    }
  }

  const handelGetMyCoursesList = async () => {
    const storedToken = window.localStorage.getItem(defaultConfig.storageTokenKeyName)!
    const AccessKey = window.localStorage.getItem(defaultConfig.storageAccessKeyName)!
    if(window && defaultConfig)  {
      const myCoursesListData = window.localStorage.getItem(defaultConfig.storageMyCoursesList)
      if(myCoursesListData && myCoursesListData != undefined) {
        try {
          const myCoursesListJson = JSON.parse(myCoursesListData)
          setMyCoursesList(myCoursesListJson)
        }
        catch(Error: any) {
            console.log("handelGetMyCoursesList myCoursesList Error", myCoursesList)
        }
      }
    }
    if(window && authConfig && (myCoursesList.length == 0))   {
      try {
        await axios.get(authConfig.backEndApiHost + 'aichat/getMyCourses.php', {
          headers: {
            Authorization: storedToken
          },
          params: {}
        }).then(res => {
          const data = res.data
          if(data && data.data && data.isEncrypted == "1")  {
            const i = data.data.slice(0, 32);
            const t = data.data.slice(-32);
            const e = data.data.slice(32, -32);
            const k = AccessKey;
            console.log("kkkkkk1234", k)
            const DecryptDataAES256GCMData = DecryptDataAES256GCM(e, i, t, k)
            console.log("kkkkkk1234", DecryptDataAES256GCMData)
            try {
              const ResJson = JSON.parse(DecryptDataAES256GCMData)
              console.log("DecryptDataAES256GCMData ResJson", ResJson)
              setMyCoursesList(ResJson.data)
              setIsLoading(false)
              window.localStorage.setItem(defaultConfig.storageMyCoursesList, JSON.stringify(ResJson))
            }
            catch(Error: any) {
              console.log("DecryptDataAES256GCMData Error", Error)
              setMyCoursesList([])
              setIsLoading(false)
            }
          }
          else {
            setMyCoursesList(data.data)
            setIsLoading(false)
            window.localStorage.setItem(defaultConfig.storageMyCoursesList, JSON.stringify(data.data))
          }
        })
      }
      catch(Error: any) {
        console.log("handelGetMyCoursesList Error", Error)
        setIsLoading(false)

        return []
      }
    }
  }

  const handelGetChatAppList = async () => {
    const storedToken = window.localStorage.getItem(defaultConfig.storageTokenKeyName)!
    const AccessKey = window.localStorage.getItem(defaultConfig.storageAccessKeyName)!
    if(window && defaultConfig)  {
      const myCoursesListData = window.localStorage.getItem(defaultConfig.storageChatApp)
      if(myCoursesListData && myCoursesListData != undefined) {
        try {
          const myCoursesListJson = JSON.parse(myCoursesListData)
          setChatApp(myCoursesListJson)
        }
        catch(Error: any) {
            console.log("handelGetChatAppList chatApp Error", chatApp)
        }
      }
    }
    if(window && authConfig && (chatApp.length == 0))   {
      try {
        await axios.get(authConfig.backEndApiHost + 'aichat/chatapp.php', {
          headers: {
            Authorization: storedToken
          },
          params: { action: 'getAppList' }
        }).then(res => {
          const data = res.data
          if(data && data.data && data.isEncrypted == "1")  {
            const i = data.data.slice(0, 32);
            const t = data.data.slice(-32);
            const e = data.data.slice(32, -32);
            const k = AccessKey;
            console.log("kkkkkk1234", k)
            const DecryptDataAES256GCMData = DecryptDataAES256GCM(e, i, t, k)
            console.log("kkkkkk1234", DecryptDataAES256GCMData)
            try {
              const ResJson = JSON.parse(DecryptDataAES256GCMData)
              console.log("DecryptDataAES256GCMData ResJson", ResJson)
              setChatApp(ResJson.data)
              setIsLoading(false)
              window.localStorage.setItem(defaultConfig.storageChatApp, JSON.stringify(ResJson))
            }
            catch(Error: any) {
              console.log("DecryptDataAES256GCMData Error", Error)
              setChatApp([])
              setIsLoading(false)
            }
          }
          else {
            setChatApp(data.data)
            setIsLoading(false)
            window.localStorage.setItem(defaultConfig.storageChatApp, JSON.stringify(data.data))
          }
        })
      }
      catch(Error: any) {
        console.log("handelGetChatAppList Error", Error)
        setIsLoading(false)

        return []
      }
    }
  }

  const handleWalletGoHome = () => {
    setPageModel("Main")
    setLeftIcon('')
    setTitle(t('AiChat') as string)
    setRightButtonText('')
  }

  const LeftIconOnClick = () => {
    switch(pageModel) {
      case 'ChatWithApp':
      case 'ChatWithCourse':
        handleWalletGoHome()
        break
    }
  }

  const RightButtonOnClick = () => {
    switch(RightButtonIcon) {
      case 'lineicons:trash-3':
        setClearButtonClickEvent(true)
        break
    }
  }

  useEffect(() => {
    setHeaderHidden(false)
    handelGetMyCoursesList()
    handelGetChatAppList()
  }, []);

  useEffect(() => {
    if(historyCounter>1) {
      setRightButtonIcon('lineicons:trash-3')
      setClearButtonClickEvent(false)
    }
    else {
      setRightButtonIcon('')
      setClearButtonClickEvent(false)
    }
  }, [historyCounter]);

  return (
    <Fragment>
      <Header Hidden={HeaderHidden} LeftIcon={LeftIcon} LeftIconOnClick={LeftIconOnClick} Title={Title} RightButtonText={RightButtonText} RightButtonOnClick={RightButtonOnClick} RightButtonIcon={RightButtonIcon}/>

      <Box
        component="main"
        sx={{
          flex: 1,
          overflowY: 'hidden',
          overflowX: 'hidden',
          marginTop: '48px', // Adjust according to the height of the AppBar
          marginBottom: '56px', // Adjust according to the height of the Footer
          paddingTop: 'env(safe-area-inset-top)'
        }}
      >
        <ContentWrapper>

          {pageModel == "Main" && (
            <Fragment>
              {isLoading && myCoursesList.length == 0 ? (
                    <Grid item xs={12} sm={12} container justifyContent="space-around">
                        <Box sx={{ mt: 6, mb: 6, display: 'flex', alignItems: 'center', flexDirection: 'column' }}>
                            <CircularProgress />
                            <Typography sx={{pt:5, pb:5}}>加载中...</Typography>
                        </Box>
                    </Grid>
                ) : (
                <Grid container spacing={0}>

                  {myCoursesList && myCoursesList.length > 0 && myCoursesList.map((item: any, index: number) => {

                  return (
                    <Grid item xs={12} sx={{ mb: 2, }} key={index}>
                      <Card>
                        <CardContent onClick={()=>handleSetChatWithApp(item)} sx={{m: 1, p: 1, pl: 3, mb: 0, pb: 0}}>
                            <Grid item xs={12} mt={2}>
                              <Typography variant='body2' sx={{ fontWeight: 'bold', color: 'text.primary', display: 'flex', alignItems: 'center' }}>
                              {item['班级名称']}
                              </Typography>
                            </Grid>
                            <Grid item xs={12} mt={1}>
                              <Typography variant='body2' sx={{ color: 'text.primary', display: 'flex', alignItems: 'left' }}>
                                课程:{item['课程名称']}
                              </Typography>
                            </Grid>
                            <Grid item xs={12} mt={1}>
                              <Typography variant='body2' sx={{ color: 'text.primary', display: 'flex', alignItems: 'left' }}>
                                教师:{item['教师姓名']}
                              </Typography>
                            </Grid>
                        </CardContent>
                      </Card>
                    </Grid>
                  )
                  })}
                </Grid>
              )}
            </Fragment>
          )}

          {pageModel == "ChatWithCourse" && (
            <Fragment>
              <ChatIndex authConfig={authConfig} app={app} historyCounter={historyCounter} setHistoryCounter={setHistoryCounter} clearButtonClickEvent={clearButtonClickEvent} />
            </Fragment>
          )}

          {pageModel == "ChatWithApp" && (
            <Fragment>
              <ChatIndex authConfig={authConfig} app={app} historyCounter={historyCounter} setHistoryCounter={setHistoryCounter} clearButtonClickEvent={clearButtonClickEvent} />
            </Fragment>
          )}

        </ContentWrapper>
      </Box>
    </Fragment>

  );
};

export default AllAiApp;
