import bs58 from 'bs58'

// ** React Imports
import { useState, ReactNode, Fragment } from 'react'

// ** MUI Components
import Grid from '@mui/material/Grid'
import Button from '@mui/material/Button'
import Checkbox from '@mui/material/Checkbox'
import TextField from '@mui/material/TextField'
import InputLabel from '@mui/material/InputLabel'
import IconButton from '@mui/material/IconButton'
import Box, { BoxProps } from '@mui/material/Box'
import FormControl from '@mui/material/FormControl'
import OutlinedInput from '@mui/material/OutlinedInput'
import { styled } from '@mui/material/styles'
import FormHelperText from '@mui/material/FormHelperText'
import InputAdornment from '@mui/material/InputAdornment'
import Typography from '@mui/material/Typography'
import MuiFormControlLabel, { FormControlLabelProps } from '@mui/material/FormControlLabel'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Third Party Imports
import * as yup from 'yup'
import { useForm, Controller } from 'react-hook-form'
import { yupResolver } from '@hookform/resolvers/yup'

import { setLocale } from 'yup';
import AddOrEditTableLanguage from 'src/types/forms/AddOrEditTableLanguage';

import Header from '../Layout/Header'
import TermsofUse from '../Setting/TermsofUse'
import PrivacyPolicy from '../Setting/PrivacyPolicy'

setLocale(AddOrEditTableLanguage);

// ** Hooks
import { useAuth } from 'src/hooks/useAuth'

// ** Configs
import themeConfig from 'src/configs/themeConfig'

// ** Layout Import
import BlankLayout from 'src/@core/layouts/BlankLayout'

const RightWrapper = styled(Box)<BoxProps>(({ theme }) => ({
  width: '100%',
  [theme.breakpoints.up('md')]: {
    maxWidth: 450
  }
}))

const BoxWrapper = styled(Box)<BoxProps>(({ theme }) => ({
  [theme.breakpoints.down('xl')]: {
    width: '100%'
  },
  [theme.breakpoints.down('md')]: {
    maxWidth: 400
  }
}))

const FormControlLabel = styled(MuiFormControlLabel)<FormControlLabelProps>(({ theme }) => ({
  '& .MuiFormControlLabel-label': {
    fontSize: '0.875rem',
    color: theme.palette.text.secondary
  }
}))

const schema = yup.object().shape({
  username: yup.string().min(3).required().label('用户名'),
  password: yup.string().min(6).required().label('密码'),
  termsofUse: yup.boolean().required('请同意使用协议').test('is-true', '请同意使用协议', value => value === true)
})

const defaultValues = {
  password: '',
  username: '',
  termsofUse: false
}

interface FormData {
  username: string
  password: string
  termsofUse: boolean
}

const Login = ({ setCurrentTab, setDisabledFooter }: any) => {
  const [showPassword, setShowPassword] = useState<boolean>(false)

  const auth = useAuth()

  const {
    control,
    setError,
    handleSubmit,
    formState: { errors }
  } = useForm({
    defaultValues,
    mode: 'onBlur',
    resolver: yupResolver(schema)
  })

  const onSubmit = (data: FormData) => {
    const { username, password } = data

    console.log("data", base58Encode(base58Encode(JSON.stringify({ username, password, rememberMe: true }))))

    function base58Encode(data: string) {
      const bytes = Buffer.from(data, 'utf8');
      const encoded = bs58.encode(bytes);
            
      return encoded;
    }

    auth.login({Data: base58Encode(base58Encode(JSON.stringify({ username, password, rememberMe: true }))), handleGoIndex, handleGoLogin}, () => {
      setError('username', {
        type: 'manual',
        message: '用户名或密码错误'
      })
    })
  }

  const [pageModel, setPageModel] = useState<string>('Login')
  const [HeaderHidden, setHeaderHidden] = useState<boolean>(true)
  const [LeftIcon, setLeftIcon] = useState<string>('')
  const [Title, setTitle] = useState<string>('Setting')
  const [RightButtonText, setRightButtonText] = useState<string>('')
  const [RightButtonIcon, setRightButtonIcon] = useState<string>('')

  const handleClickTermsOfUseButton = () => {
    setHeaderHidden(false)
    setPageModel('TermsOfUse')
    setLeftIcon('mdi:arrow-left-thin')
    setTitle('使用协议')
    setRightButtonText('')
    setRightButtonIcon('')
  }

  const handleClickPrivacyPolicyButton = () => {
    setHeaderHidden(false)
    setPageModel('PrivacyPolicy')
    setLeftIcon('mdi:arrow-left-thin')
    setTitle('隐私政策')
    setRightButtonText('')
    setRightButtonIcon('')
  }

  const handleGoIndex = () => {
    setCurrentTab('Index')
    setDisabledFooter(false)
  }

  const handleGoLogin = () => {
    setPageModel('Login')
    setLeftIcon('')
    setTitle('Setting')
    setRightButtonText('QR')
    setRightButtonIcon('')
    setHeaderHidden(true)
  }
  
  const LeftIconOnClick = () => {
    switch(pageModel) {
      case 'PrivacyPolicy':
      case 'TermsOfUse':
        handleGoLogin()
        break
    }
  }
  
  const RightButtonOnClick = () => {
    switch(pageModel) {
        case 'Contacts':
          break
      }
  }

  return (
    <>
    <Header Hidden={HeaderHidden} LeftIcon={LeftIcon} LeftIconOnClick={LeftIconOnClick} Title={Title} RightButtonText={RightButtonText} RightButtonOnClick={RightButtonOnClick} RightButtonIcon={RightButtonIcon}/>

    {pageModel == "Login" && (
      <Box className='content-right'
        sx={{
          height: '100%',
          backgroundColor: 'background.paper'
        }}
      >
        <RightWrapper sx={{}}>
          <Box
            sx={{
              p: 12,
              height: '100%',
              display: 'flex',
              alignItems: 'center',
              justifyContent: 'center',
              backgroundColor: 'background.paper'
            }}
          >
            <BoxWrapper>
              <Box
                sx={{
                  display: 'flex',
                  alignItems: 'center',
                  justifyContent: 'center'
                }}
              >
                <Typography
                  variant='h6'
                  sx={{
                    ml: 3,
                    lineHeight: 1,
                    fontWeight: 600,
                    textTransform: 'uppercase',
                    fontSize: '1.5rem !important'
                  }}
                >
                  {themeConfig.templateName}
                </Typography>
              </Box>
              <form noValidate autoComplete='off' onSubmit={handleSubmit(onSubmit)}>
                <FormControl fullWidth sx={{ mb: 4, mt: 12 }}>
                  <Controller
                    name='username'
                    control={control}
                    rules={{ required: true }}
                    render={({ field: { value, onChange, onBlur } }) => (
                      <TextField
                        size='small'
                        autoFocus
                        label='用户名'
                        value={value}
                        onBlur={onBlur}
                        onChange={onChange}
                        error={Boolean(errors.username)}
                        placeholder=''
                      />
                    )}
                  />
                  {errors.username && <FormHelperText sx={{ color: 'error.main' }}>{errors.username.message}</FormHelperText>}
                </FormControl>
                <FormControl fullWidth sx={{ mt: 1 }}>
                  <InputLabel htmlFor='auth-login-v2-password' error={Boolean(errors.password)}>
                    密码
                  </InputLabel>
                  <Controller
                    name='password'
                    control={control}
                    rules={{ required: true }}
                    render={({ field: { value, onChange, onBlur } }) => (
                      <OutlinedInput
                        size='small'
                        value={value}
                        onBlur={onBlur}
                        label='密码'
                        onChange={onChange}
                        id='auth-login-v2-password'
                        error={Boolean(errors.password)}
                        type={showPassword ? 'text' : 'password'}
                        endAdornment={
                          <InputAdornment position='end'>
                            <IconButton
                              edge='end'
                              onMouseDown={e => e.preventDefault()}
                              onClick={() => setShowPassword(!showPassword)}
                            >
                              <Icon icon={showPassword ? 'mdi:eye-outline' : 'mdi:eye-off-outline'} fontSize={20} />
                            </IconButton>
                          </InputAdornment>
                        }
                      />
                    )}
                  />
                  {errors.password && (
                    <FormHelperText sx={{ color: 'error.main' }} id=''>
                      {errors.password.message}
                    </FormHelperText>
                  )}
                </FormControl>
                <Box sx={{ display: 'flex', alignItems: 'center', flexWrap: 'wrap', justifyContent: 'space-between' }} >
                  <FormControl fullWidth sx={{ mb: 2, mt: 2 }}>
                    <Controller
                      name='termsofUse'
                      control={control}
                      rules={{ required: true }}
                      render={({ field: { value, onChange } }) => (
                        <FormControlLabel
                          control={<Checkbox checked={value} onChange={onChange} />}
                          label={
                            <Fragment>
                              <span>我同意 </span>
                              <Button variant='text' onClick={()=>handleClickTermsOfUseButton()} sx={{ml: 1}}>使用协议</Button>
                              <Button variant='text' onClick={()=>handleClickPrivacyPolicyButton()} sx={{mx: 1}}>隐私政策</Button>
                            </Fragment>
                          }
                        />
                      )}
                    />
                    {errors.termsofUse && <FormHelperText sx={{ color: 'error.main' }}>{errors.termsofUse.message}</FormHelperText>}
                  </FormControl>
                </Box>
                <Button fullWidth size='medium' type='submit' variant='contained' sx={{ mb: 7 }}>
                  登录
                </Button>
              </form>
            </BoxWrapper>
          </Box>
        </RightWrapper>
      </Box>
    )}
    {pageModel == 'PrivacyPolicy' && ( 
      <Grid container spacing={6}>
        <Grid item xs={12}>
          <PrivacyPolicy />
        </Grid>
      </Grid>
    )}

    {pageModel == 'TermsOfUse' && ( 
      <Grid container spacing={6}>
        <Grid item xs={12}>
          <TermsofUse />
        </Grid>
      </Grid>
    )}
    
    </>
  )
}

Login.getLayout = (page: ReactNode) => <BlankLayout>{page}</BlankLayout>

Login.guestGuard = true

export default Login
