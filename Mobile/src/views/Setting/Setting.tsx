// ** React Imports
import { useState, useEffect, Fragment } from 'react'

import { Clipboard } from '@capacitor/clipboard';

// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import Grid from '@mui/material/Grid'
import Typography from '@mui/material/Typography'
import CustomAvatar from '../../@core/components/mui/avatar'
import IconButton from '@mui/material/IconButton'
import TextField from '@mui/material/TextField'
import { getInitials } from '../../@core/utils/get-initials'
import Radio from '@mui/material/Radio'
import RadioGroup from '@mui/material/RadioGroup'
import FormControlLabel from '@mui/material/FormControlLabel'

// ** MUI Imports
import Button from '@mui/material/Button'
import Icon from '../../@core/components/icon'
import toast from 'react-hot-toast'
import authConfig from '../../configs/auth'
import { useSettings } from '../../@core/hooks/useSettings'

// ** Third Party Import
import { useTranslation } from 'react-i18next'

import { styled } from '@mui/material/styles'
import Header from '../Layout/Header'
import TermsofUse from './TermsofUse'
import PrivacyPolicy from './PrivacyPolicy'
import Link from 'next/link'

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

const Setting = ({ encryptWalletDataKey, setEncryptWalletDataKey }: any) => {

  // ** Hook
  const { t, i18n } = useTranslation()
  const { settings, saveSettings } = useSettings()

  const contentHeightFixed = {}

  const [currentAddress, setCurrentAddress] = useState<string>("")
  const [chooseWallet, setChooseWallet] = useState<any>(null)

  const [pageModel, setPageModel] = useState<string>('MainSetting')
  const [HeaderHidden, setHeaderHidden] = useState<boolean>(false)
  const [LeftIcon, setLeftIcon] = useState<string>('')
  const [Title, setTitle] = useState<string>(t('Setting') as string)
  const [RightButtonText, setRightButtonText] = useState<string>('')
  const [RightButtonIcon, setRightButtonIcon] = useState<string>('')

  const [contactName, setContactName] = useState<string>('')
  const [contactAddress, setContactAddress] = useState<string>('')
  const [contactsAll, setContactsAll] = useState<any>({})
  const [counter, setCounter] = useState<number>(0)
  const [searchContactkeyWord, setSearchContactkeyWord] = useState<string>('')
  const [languageValue, setLanguageValue] = useState<string>('Zh')
  const [themeValue, setThemeValue] = useState<string>(settings.mode)
  const [currencyValue, setCurrencyValue] = useState<string>('us')
  const [networkValue, setNetworkValue] = useState<string>('mainnet')

  const [tokenName, setTokenName] = useState<string>('TokenName')
  const [tokenTicker, setTokenTicker] = useState<string>('AOTN')
  const [tokenTotalBalance, setTokenTotalBalance] = useState<string>('1000000')
  const [tokenLogo, setTokenLogo] = useState<string>('_A-OtzzfCZGUkVdf_Ajs6WLYIJySKI6SBmTzGKXsFG4')
  const [tokenDenomination, setTokenDenomination] = useState<string>('3')
  const [createTokenData, setCreateTokenData] = useState<any>(null)

  const [uploadingButton, setUploadingButton] = useState<string>(`${t('Submit')}`)
  const [isDisabledButton, setIsDisabledButton] = useState<boolean>(false)
  
  const LanguageArray = [
        {name:'English', value:'en'},
        {name:'Chinese', value:'zh-CN'},
        {name:'Korean', value:'Kr'},
        {name:'Russia', value:'Ru'}
  ]
  const themeArray = [
    {name:'Dark', value:'dark'},
    {name:'Light', value:'light'}
  ]
  const currencyArray = [
    {name:'United Status', value:'us'}
  ]
  const networkArray = [
    {name:'Main Network', value:'mainnet'}
  ]

  useEffect(() => {

    i18n.changeLanguage('Zh')

  }, []);


  const handleWalletGoHome = () => {
    setRefreshWalletData(refreshWalletData+1)
    setPageModel('MainSetting')
    setLeftIcon('')
    setTitle(t('Setting') as string)
    setRightButtonText(t('QR') as string)
    setRightButtonIcon('')
  }
  
  const LeftIconOnClick = () => {
    switch(pageModel) {
      case 'MainSetting':
        handleWalletGoHome()
        break
      case 'General':
      case 'Contacts':
      case 'Support':
      case 'SecurityPrivacy':
        handleWalletGoHome()
        break
      case 'NewContact':
        handleClickContactsButton()
        break
      case 'Language':
        handleClickGeneralButton()
        break
      case 'Theme':
        handleClickGeneralButton()
        break
      case 'Currency':
        handleClickGeneralButton()
        break
      case 'Network':
        handleClickGeneralButton()
        break
      case 'CreateToken':
        handleClickGeneralButton()
        break
      case 'PrivacyPolicy':
      case 'TermsOfUse':
        handleClickSecurityPrivacyButton()
        break
    }
  }
  
  const RightButtonOnClick = () => {
    switch(pageModel) {
        case 'Contacts':
            handleClickNewContactButton()
          break
      }
  }
    
  const [refreshWalletData, setRefreshWalletData] = useState<number>(0)

  useEffect(() => {
    setHeaderHidden(false)
    setRightButtonIcon('')
  }, []);
  
  const handleContactSave = () => {
    if(contactAddress && contactAddress.length == 43) {
        setChivesContacts(contactAddress, contactName, encryptWalletDataKey)
        handleClickContactsButton()
    }
  }
  
  const handleContactDelete = (Address: string) => {
    deleteChivesContacts(Address, encryptWalletDataKey)
    handleClickContactsButton()
  }

  const handleClickContactsButton = () => {
    setCounter(counter + 1)
    setPageModel('Contacts')
    setLeftIcon('mdi:arrow-left-thin')
    setTitle(t('Contacts') as string)
    setRightButtonText(t('') as string)
    setRightButtonIcon('mdi:add')
    setContactAddress('')
    setContactName('')
    setSearchContactkeyWord('')
  }

  const handleClickSecurityPrivacyButton = () => {
    setCounter(counter + 1)
    setPageModel('SecurityPrivacy')
    setLeftIcon('mdi:arrow-left-thin')
    setTitle(t('Security & Privacy') as string)
    setRightButtonText(t('') as string)
    setRightButtonIcon('')
    setContactAddress('')
    setContactName('')
    setSearchContactkeyWord('')
  }

  const handleClickGeneralButton = () => {
    setCounter(counter + 1)
    setPageModel('General')
    setLeftIcon('mdi:arrow-left-thin')
    setTitle(t('General Setting') as string)
    setRightButtonText(t('') as string)
    setRightButtonIcon('')
  }

  const handleClickLanguageButton = () => {
    setCounter(counter + 1)
    setPageModel('Language')
    setLeftIcon('mdi:arrow-left-thin')
    setTitle(t('Language') as string)
    setRightButtonText(t('') as string)
    setRightButtonIcon('')
  }

  const handleClickThemeButton = () => {
    setCounter(counter + 1)
    setPageModel('Theme')
    setLeftIcon('mdi:arrow-left-thin')
    setTitle(t('Theme') as string)
    setRightButtonText(t('') as string)
    setRightButtonIcon('')
  }

  const handleClickCurrencyButton = () => {
    setCounter(counter + 1)
    setPageModel('Currency')
    setLeftIcon('mdi:arrow-left-thin')
    setTitle(t('Currency') as string)
    setRightButtonText(t('') as string)
    setRightButtonIcon('')
  }

  const handleClickNetworkButton = () => {
    setCounter(counter + 1)
    setPageModel('Network')
    setLeftIcon('mdi:arrow-left-thin')
    setTitle(t('Network') as string)
    setRightButtonText(t('') as string)
    setRightButtonIcon('')
  }

  const handleClickCreateTokenButton = () => {
    setCounter(counter + 1)
    setPageModel('CreateToken')
    setLeftIcon('mdi:arrow-left-thin')
    setTitle(t('Create Token') as string)
    setRightButtonText(t('') as string)
    setRightButtonIcon('')
  }

  const handleCreateTokenButton = async () => {
    const validateInputs = () => {
      if (tokenName == '') {
        toast.error(t('Token name must input') as string, { duration: 1000, position: 'top-center' });

        return false;
      }
      if (tokenTicker == '') {
        toast.error(t('Token ticker must input') as string, { duration: 1000, position: 'top-center' });

        return false;
      }
      if (tokenTotalBalance == '' || Number(tokenTotalBalance) <= 0) {
        toast.error(t('Token total issue amount must input and more than zero') as string, { duration: 1000, position: 'top-center' });

        return false;
      }
      if (tokenLogo.length != 43) {
        toast.error(t('Token logo must a ar tx id that storage a image') as string, { duration: 1000, position: 'top-center' });

        return false;
      }
      if (Number(tokenDenomination) <= 0 || Number(tokenDenomination) > 12) {
        toast.error(t('Token denomination must be a integer between 1 and 12') as string, { duration: 1000, position: 'top-center' });

        return false;
      }

      return true;
    };
  
    if (!validateInputs()) {
      
      return;
    }
  
    setIsDisabledButton(true);
    setUploadingButton(t('Summitting...') as string);
  
    try {
      let TokenProcessTxId: any = null;
      do {
        TokenProcessTxId = await AoCreateProcessAuto(chooseWallet.jwk);
        console.log("TokenProcessTxId", TokenProcessTxId);
      } while (TokenProcessTxId && TokenProcessTxId.length != 43);
  
      const tokenInfo = {
        Name: tokenName,
        Ticker: tokenTicker,
        Balance: Number(tokenTotalBalance).toFixed(0),
        Logo: tokenLogo,
        Denomination: tokenDenomination
      };
  
      const createToken = async () => {
        let LoadBlueprintToken: any = await AoLoadBlueprintToken(chooseWallet.jwk, TokenProcessTxId, tokenInfo);
        console.log("handleTokenCreate LoadBlueprintToken:", LoadBlueprintToken);
        while (LoadBlueprintToken && LoadBlueprintToken.status == 'error') {
          await sleep(6000);
          LoadBlueprintToken = await AoLoadBlueprintToken(chooseWallet.jwk, TokenProcessTxId, tokenInfo);
        }
  
        const AoDryRunBalance = await AoTokenBalanceDryRun(TokenProcessTxId, currentAddress);
        if (AoDryRunBalance) {
          setCounter(counter + 1);

          return { Token: TokenProcessTxId, Balance: FormatBalance(AoDryRunBalance, Number(tokenDenomination)) };
        }

      };
  
      const result: any = await createToken();
      const TokenGetMap: any = await AoTokenInfoDryRun(result.Token)
      if(TokenGetMap) {
        setCreateTokenData({TokenId: result.Token, TokenName: TokenGetMap.Name, TokenData: TokenGetMap})
        handleSelectTokenAndSave({TokenId: result.Token}, TokenGetMap)
      }
      console.log("createTokenData", createTokenData);
  
      setUploadingButton(t('Submit') as string);
      setIsDisabledButton(false);
    } 
    catch (error) {
      console.error("handleTokenCreate Error:", error);
      setUploadingButton(t('Submit') as string);
      setIsDisabledButton(false);
    }
  };


  const handleClickNewContactButton = () => {
    setPageModel('NewContact')
    setLeftIcon('mdi:arrow-left-thin')
    setTitle(t('New Contact') as string)
    setRightButtonText(t('') as string)
    setRightButtonIcon('')
    setContactAddress('')
    setContactName('')
  }

  const handleSelectContact = (Address: string, Name: string) => {
    setContactAddress(Address)
    setContactName(Name)
    setPageModel('NewContact')
    setLeftIcon('mdi:arrow-left-thin')
    setTitle(t('Edit Conact') as string)
    setRightButtonText(t('') as string)
    setRightButtonIcon('')
  }

  const handleSelectLanguage = (Language: 'en' | 'zh-CN' | 'Ru' | 'Kr') => {
    setLanguageValue(Language)
    setTitle(Language)
    i18n.changeLanguage(Language)
    setChivesLanguage(Language)
  }

  const handleSelectTheme = (Theme: string) => {
    console.log("Theme", Theme)
    setThemeValue(Theme)
    setTitle(Theme)

    //@ts-ignore
    saveSettings({ ...settings, ['mode']: Theme })
  }

  const handleSelectCurrency = (Currency: string) => {
    setCurrencyValue(Currency)
    setTitle(Currency)
  }

  const handleSelectNetwork = (Network: string) => {
    setNetworkValue(Network)
    setTitle(Network)
  }

  const handleSelectTokenAndSave = async (Token: any, TokenData: any) => {
    setIsDisabledButton(true)
    const WantToSaveTokenProcessTxIdData = await MyProcessTxIdsAddToken(chooseWallet.jwk, authConfig.AoConnectMyProcessTxIds, Token.TokenId, '200', TokenData.Name, JSON.stringify(TokenData) )
    setIsDisabledButton(false)
    if(WantToSaveTokenProcessTxIdData?.msg?.Messages && WantToSaveTokenProcessTxIdData?.msg?.Messages[0]?.Data)  {
      toast.success(t(WantToSaveTokenProcessTxIdData?.msg?.Messages[0]?.Data) as string, { duration: 2500, position: 'top-center' })
      addMyAoToken(currentAddress, {TokenId: Token.TokenId, TokenSort: '200', TokenName: TokenData.Name, TokenData: TokenData}, encryptWalletDataKey)
    }
  }

  const handleClickTermsOfUseButton = () => {
    setPageModel('TermsOfUse')
    setLeftIcon('mdi:arrow-left-thin')
    setTitle(t('Terms of Use') as string)
    setRightButtonText('')
    setRightButtonIcon('')
  }

  const handleClickPrivacyPolicyButton = () => {
    setPageModel('PrivacyPolicy')
    setLeftIcon('mdi:arrow-left-thin')
    setTitle(t('Privacy Policy') as string)
    setRightButtonText('')
    setRightButtonIcon('')
  }

  const handleClickCheckPinCodeButton = () => {
    setPageModel('CheckPinCode')
    setTitle(t('Check Pin Code') as string)
    setLeftIcon('')
    setRightButtonText('')
    setRightButtonIcon('')
    setLeftIcon('')
  }

  const handleClickSetPinCodeButton = () => {
    setPageModel('SetPinCode')
    setTitle(t('Set Pin Code') as string)
    setLeftIcon('')
    setRightButtonText('')
    setRightButtonIcon('')
    setLeftIcon('')
  }

  const setPinKeySuccess = (OldKay: string, NewKey: string) => {
    resetPasswordForWallet(OldKay, NewKey)
    resetChivesWalletNickNameEncryptedKey(OldKay, NewKey)
    resetChivesWalletsEncryptedKey(OldKay, NewKey)
    resetChivesContactsEncryptedKey(OldKay, NewKey)
    toast.success(t('Change pin code success') as string, { duration: 2500, position: 'top-center' })
    handleWalletGoHome()
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
              <Grid container spacing={2}>
                <Grid item xs={12} sx={{height: 'calc(100%)'}}>
                    <Grid container spacing={2}>
                        <Grid item xs={12} sx={{ py: 1 }}>
                          <Card>
                            <Box sx={{ display: 'flex', alignItems: 'center', px: 2, py: 0.7}}>
                                <IconButton sx={{ p: 0 }} onClick={()=>handleClickGeneralButton()}>
                                    <Icon icon='oui:integration-general' fontSize={38} />
                                </IconButton>
                                <Box sx={{ cursor: 'pointer', ml: 2, display: 'flex', flexDirection: 'column', width: '100%' }} onClick={()=>handleClickGeneralButton()}
                                    >
                                    <Typography sx={{ 
                                      color: 'text.primary',
                                      overflow: 'hidden',
                                      textOverflow: 'ellipsis',
                                      whiteSpace: 'nowrap',
                                    }}
                                    >
                                    {t('General') as string}
                                    </Typography>
                                    <Box sx={{ display: 'flex'}}>
                                    <Typography variant='body2' sx={{ 
                                        color: `secondary.primary`, 
                                        overflow: 'hidden',
                                        textOverflow: 'ellipsis',
                                        whiteSpace: 'nowrap',
                                        flex: 1
                                    }}>
                                        {t('Edit language, currency and theme') as string}
                                    </Typography>
                                    </Box>
                                </Box>
                                <Box textAlign="right">
                                    <IconButton sx={{ p: 0 }} onClick={()=>handleClickGeneralButton()}>
                                        <Icon icon='mdi:chevron-right' fontSize={30} />
                                    </IconButton>
                                </Box>
                            </Box>
                          </Card>
                        </Grid>
                        <Grid item xs={12} sx={{ py: 1 }}>
                          <Card>
                            <Box sx={{ display: 'flex', alignItems: 'center', px: 2, py: 0.7}}>
                              <IconButton sx={{ p: 0, ml: 1 }} onClick={()=>handleClickContactsButton()}>
                                <Icon icon='mdi:contact-mail-outline' fontSize={34} />
                              </IconButton>
                              <Box sx={{ cursor: 'pointer', ml: 2.5, display: 'flex', flexDirection: 'column', width: '100%' }} onClick={()=>handleClickContactsButton()}
                                >
                                <Typography sx={{ 
                                  color: 'text.primary',
                                  overflow: 'hidden',
                                  textOverflow: 'ellipsis',
                                  whiteSpace: 'nowrap',
                                }}
                                >
                                  {t('Contacts') as string}
                                </Typography>
                                <Box sx={{ display: 'flex'}}>
                                  <Typography variant='body2' sx={{ 
                                    color: `secondary.primary`, 
                                    overflow: 'hidden',
                                    textOverflow: 'ellipsis',
                                    whiteSpace: 'nowrap',
                                    flex: 1
                                  }}>
                                    {t('Manage your contacts') as string}
                                  </Typography>
                                </Box>
                              </Box>
                              <Box textAlign="right">
                                <IconButton sx={{ p: 0 }} onClick={()=>handleClickContactsButton()}>
                                    <Icon icon='mdi:chevron-right' fontSize={30} />
                                </IconButton>
                              </Box>
                            </Box>
                          </Card>
                        </Grid>
                        <Grid item xs={12} sx={{ py: 1 }}>
                          <Card>
                            <Box sx={{ display: 'flex', alignItems: 'center', px: 2, py: 0.7}}>
                              <IconButton sx={{ p: 0, ml: 1 }} onClick={()=>handleClickSecurityPrivacyButton()}>
                                <Icon icon='mdi:security-lock-outline' fontSize={34} />
                              </IconButton>
                              <Box sx={{ cursor: 'pointer', ml: 2.5, display: 'flex', flexDirection: 'column', width: '100%' }} onClick={()=>handleClickSecurityPrivacyButton()}
                                >
                                <Typography sx={{ 
                                  color: 'text.primary',
                                  overflow: 'hidden',
                                  textOverflow: 'ellipsis',
                                  whiteSpace: 'nowrap',
                                }}
                                >
                                  {t('Security & Privacy') as string}
                                </Typography>
                                <Box sx={{ display: 'flex'}}>
                                  <Typography variant='body2' sx={{ 
                                    color: `secondary.primary`, 
                                    overflow: 'hidden',
                                    textOverflow: 'ellipsis',
                                    whiteSpace: 'nowrap',
                                    flex: 1
                                  }}>
                                    {t('Management applications, etc') as string}
                                  </Typography>
                                </Box>
                              </Box>
                              <Box textAlign="right">
                                <IconButton sx={{ p: 0 }} onClick={()=>handleClickSecurityPrivacyButton()}>
                                    <Icon icon='mdi:chevron-right' fontSize={30} />
                                </IconButton>
                              </Box>
                            </Box>
                          </Card>
                        </Grid>
                        <Grid item xs={12} sx={{ py: 1 }}>
                          <Card>
                            <Box sx={{ display: 'flex', alignItems: 'center', px: 2, py: 0.7}}>
                              <IconButton sx={{ p: 0, ml: 1 }} onClick={()=>null}>
                                <Icon icon='material-symbols:support-agent' fontSize={34} />
                              </IconButton>
                              <Box sx={{ ml: 2.5, display: 'flex', flexDirection: 'column', width: '100%' }} onClick={()=>null}
                                >
                                <Typography sx={{ 
                                  color: 'text.primary',
                                  overflow: 'hidden',
                                  textOverflow: 'ellipsis',
                                  whiteSpace: 'nowrap',
                                }}
                                >
                                  {t('Support') as string}
                                </Typography>
                                <Box sx={{ display: 'flex'}}>
                                  <Typography variant='body2' sx={{ 
                                    color: `secondary.primary`, 
                                    overflow: 'hidden',
                                    textOverflow: 'ellipsis',
                                    whiteSpace: 'nowrap',
                                    flex: 1
                                  }}>
                                    {t('Contact our customer support') as string}
                                  </Typography>
                                  <Link href={authConfig.Github} target='_blank'>
                                    <Typography variant='body2' sx={{ 
                                      color: `secondary.primary`, 
                                      overflow: 'hidden',
                                      textOverflow: 'ellipsis',
                                      whiteSpace: 'nowrap',
                                      flex: 1
                                    }}>
                                      {t('Github') as string}
                                    </Typography>
                                  </Link>
                                </Box>
                              </Box>
                            </Box>
                          </Card>
                        </Grid>
                        <Grid item xs={12} sx={{ py: 1 }}>
                          <Card>
                            <Box sx={{ display: 'flex', alignItems: 'center', px: 2, py: 0.7}}>
                              <IconButton sx={{ p: 0, ml: 1 }} onClick={()=>null}>
                                <Icon icon='material-symbols:help-outline' fontSize={34} />
                              </IconButton>
                              <Box sx={{ ml: 2.5, display: 'flex', flexDirection: 'column', width: '100%' }} onClick={()=>null}
                                >
                                <Typography sx={{ 
                                  color: 'text.primary',
                                  overflow: 'hidden',
                                  textOverflow: 'ellipsis',
                                  whiteSpace: 'nowrap',
                                }}
                                >
                                  {t('Version') as string}
                                </Typography>
                                <Box sx={{ display: 'flex'}}>
                                  <Typography variant='body2' sx={{ 
                                    color: `secondary.primary`, 
                                    overflow: 'hidden',
                                    textOverflow: 'ellipsis',
                                    whiteSpace: 'nowrap',
                                    flex: 1
                                  }}>
                                    {authConfig.AppVersion}
                                  </Typography>
                                </Box>
                              </Box>
                            </Box>
                          </Card>
                        </Grid>
                    </Grid>
                </Grid>
              </Grid>
            )}

            {pageModel == 'General' && ( 
              <Grid container spacing={2}>
                <Grid item xs={12} sx={{height: 'calc(100%)'}}>
                    <Grid container spacing={2}>
                        <Grid item xs={12} sx={{ py: 1 }}>
                          <Card>
                            <Box sx={{ display: 'flex', alignItems: 'center', px: 2, py: 0.7}}>
                                <IconButton sx={{ p: 0 }} onClick={()=>handleClickLanguageButton()}>
                                    <Icon icon='clarity:language-line' fontSize={38} />
                                </IconButton>
                                <Box sx={{ cursor: 'pointer', ml: 2, display: 'flex', flexDirection: 'column', width: '100%' }} onClick={()=>handleClickLanguageButton()}
                                    >
                                    <Typography sx={{ 
                                    color: 'text.primary',
                                    overflow: 'hidden',
                                    textOverflow: 'ellipsis',
                                    whiteSpace: 'nowrap',
                                    }}
                                    >
                                    {t('Language') as string}
                                    </Typography>
                                    <Box sx={{ display: 'flex'}}>
                                    <Typography variant='body2' sx={{ 
                                        color: `secondary.primary`, 
                                        overflow: 'hidden',
                                        textOverflow: 'ellipsis',
                                        whiteSpace: 'nowrap',
                                        flex: 1
                                    }}>
                                        {t('Language') as string}
                                    </Typography>
                                    </Box>
                                </Box>
                                <Box textAlign="right">
                                    <IconButton sx={{ p: 0 }} onClick={()=>handleClickLanguageButton()}>
                                        <Icon icon='mdi:chevron-right' fontSize={30} />
                                    </IconButton>
                                </Box>
                            </Box>
                          </Card>
                        </Grid>
                        <Grid item xs={12} sx={{ py: 1 }}>
                          <Card>
                            <Box sx={{ display: 'flex', alignItems: 'center', px: 2, py: 0.7}}>
                              <IconButton sx={{ p: 0, ml: 1 }} onClick={()=>handleClickThemeButton()}>
                                <Icon icon='line-md:light-dark' fontSize={34} />
                              </IconButton>
                              <Box sx={{ cursor: 'pointer', ml: 2.5, display: 'flex', flexDirection: 'column', width: '100%' }} onClick={()=>handleClickThemeButton()}
                                >
                                <Typography sx={{ 
                                  color: 'text.primary',
                                  overflow: 'hidden',
                                  textOverflow: 'ellipsis',
                                  whiteSpace: 'nowrap',
                                }}
                                >
                                  {t('Theme') as string}
                                </Typography>
                                <Box sx={{ display: 'flex'}}>
                                  <Typography variant='body2' sx={{ 
                                    color: `secondary.primary`, 
                                    overflow: 'hidden',
                                    textOverflow: 'ellipsis',
                                    whiteSpace: 'nowrap',
                                    flex: 1
                                  }}>
                                    {t('Theme') as string}
                                  </Typography>
                                </Box>
                              </Box>
                              <Box textAlign="right">
                                <IconButton sx={{ p: 0 }} onClick={()=>handleClickThemeButton()}>
                                    <Icon icon='mdi:chevron-right' fontSize={30} />
                                </IconButton>
                              </Box>
                            </Box>
                          </Card>
                        </Grid>
                        <Grid item xs={12} sx={{ py: 1 }}>
                          <Card>
                            <Box sx={{ display: 'flex', alignItems: 'center', px: 2, py: 0.7}}>
                              <IconButton sx={{ p: 0, ml: 1 }} onClick={()=>handleClickCurrencyButton()}>
                                <Icon icon='mdi:dollar' fontSize={34} />
                              </IconButton>
                              <Box sx={{ cursor: 'pointer', ml: 2.5, display: 'flex', flexDirection: 'column', width: '100%' }} onClick={()=>handleClickCurrencyButton()}
                                >
                                <Typography sx={{ 
                                  color: 'text.primary',
                                  overflow: 'hidden',
                                  textOverflow: 'ellipsis',
                                  whiteSpace: 'nowrap',
                                }}
                                >
                                  {t('Currency') as string}
                                </Typography>
                                <Box sx={{ display: 'flex'}}>
                                  <Typography variant='body2' sx={{ 
                                    color: `secondary.primary`, 
                                    overflow: 'hidden',
                                    textOverflow: 'ellipsis',
                                    whiteSpace: 'nowrap',
                                    flex: 1
                                  }}>
                                    {t('Currency') as string}
                                  </Typography>
                                </Box>
                              </Box>
                              <Box textAlign="right">
                                <IconButton sx={{ p: 0 }} onClick={()=>handleClickCurrencyButton()}>
                                    <Icon icon='mdi:chevron-right' fontSize={30} />
                                </IconButton>
                              </Box>
                            </Box>
                          </Card>
                        </Grid>
                        <Grid item xs={12} sx={{ py: 1 }}>
                          <Card>
                            <Box sx={{ display: 'flex', alignItems: 'center', px: 2, py: 0.7}}>
                              <IconButton sx={{ p: 0, ml: 1 }} onClick={()=>handleClickNetworkButton()}>
                                <Icon icon='tabler:world-dollar' fontSize={34} />
                              </IconButton>
                              <Box sx={{ cursor: 'pointer', ml: 2.5, display: 'flex', flexDirection: 'column', width: '100%' }} onClick={()=>handleClickNetworkButton()}
                                >
                                <Typography sx={{ 
                                  color: 'text.primary',
                                  overflow: 'hidden',
                                  textOverflow: 'ellipsis',
                                  whiteSpace: 'nowrap',
                                }}
                                >
                                  {t('Network') as string}
                                </Typography>
                                <Box sx={{ display: 'flex'}}>
                                  <Typography variant='body2' sx={{ 
                                    color: `secondary.primary`, 
                                    overflow: 'hidden',
                                    textOverflow: 'ellipsis',
                                    whiteSpace: 'nowrap',
                                    flex: 1
                                  }}>
                                    {t('Network') as string}
                                  </Typography>
                                </Box>
                              </Box>
                              <Box textAlign="right">
                                  <IconButton sx={{ p: 0 }} onClick={()=>handleClickLanguageButton()}>
                                      <Icon icon='mdi:chevron-right' fontSize={30} />
                                  </IconButton>
                              </Box>
                            </Box>
                          </Card>
                        </Grid>
                        <Grid item xs={12} sx={{ py: 1 }}>
                          <Card>
                            <Box sx={{ display: 'flex', alignItems: 'center', px: 2, py: 0.7}}>
                              <IconButton sx={{ p: 0, ml: 1 }} onClick={()=>handleClickCreateTokenButton()}>
                                <Icon icon='material-symbols:token-outline' fontSize={34} />
                              </IconButton>
                              <Box sx={{ cursor: 'pointer', ml: 2.5, display: 'flex', flexDirection: 'column', width: '100%' }} onClick={()=>handleClickCreateTokenButton()}
                                >
                                <Typography sx={{ 
                                  color: 'text.primary',
                                  overflow: 'hidden',
                                  textOverflow: 'ellipsis',
                                  whiteSpace: 'nowrap',
                                }}
                                >
                                  {t('Create Token') as string}
                                </Typography>
                                <Box sx={{ display: 'flex'}}>
                                  <Typography variant='body2' sx={{ 
                                    color: `secondary.primary`, 
                                    overflow: 'hidden',
                                    textOverflow: 'ellipsis',
                                    whiteSpace: 'nowrap',
                                    flex: 1
                                  }}>
                                    {t('Create Token') as string}
                                  </Typography>
                                </Box>
                              </Box>
                              <Box textAlign="right">
                                  <IconButton sx={{ p: 0 }} onClick={()=>handleClickLanguageButton()}>
                                      <Icon icon='mdi:chevron-right' fontSize={30} />
                                  </IconButton>
                              </Box>
                            </Box>
                          </Card>
                        </Grid>
                    </Grid>
                </Grid>
              </Grid>
            )}

            {pageModel == 'Contacts' && ( 
              <Grid container spacing={2}>
                <Grid item xs={12} sx={{height: 'calc(100%)'}}>
                    <Grid container spacing={2}>
                      <TextField
                        fullWidth
                        size='small'
                        value={searchContactkeyWord}
                        placeholder={t('Search Contact') as string}
                        sx={{ '& .MuiInputBase-root': { borderRadius: 2 }, mb: 3 }}
                        onChange={(e: any)=>{
                            setSearchContactkeyWord(e.target.value)
                            const searchChivesContactsData = searchChivesContacts(e.target.value, encryptWalletDataKey)
                            setContactsAll(searchChivesContactsData)
                            console.log("e.target.value", e.target.value)
                        }}
                      />
                    </Grid>
                    <Grid container spacing={2}>
                    {Object.keys(contactsAll).map((Address: any, index: number) => {

                      return (
                        <Grid item xs={12} sx={{ py: 1 }} key={index}>
                          <Card>
                            <Box sx={{ display: 'flex', alignItems: 'center', px: 2, py: 0.7}}>
                              <CustomAvatar
                                skin='light'
                                color={'primary'}
                                sx={{ mr: 3, width: 38, height: 38, fontSize: '1.5rem' }}
                              >
                                {getInitials(Address).toUpperCase()}
                              </CustomAvatar>
                              <Box sx={{ cursor: 'pointer', display: 'flex', flexDirection: 'column', width: '100%' }} onClick={()=>handleSelectContact(Address, contactsAll[Address])}
                                >
                                <Typography sx={{ 
                                  color: 'text.primary',
                                  overflow: 'hidden',
                                  textOverflow: 'ellipsis',
                                  whiteSpace: 'nowrap',
                                }}
                                >
                                  {contactsAll[Address]}
                                </Typography>
                                <Box sx={{ display: 'flex'}}>
                                  <Typography variant='body2' sx={{ 
                                    color: `primary.dark`, 
                                    overflow: 'hidden',
                                    textOverflow: 'ellipsis',
                                    whiteSpace: 'nowrap',
                                    flex: 1
                                  }}>
                                    {Address}
                                  </Typography>
                                  
                                </Box>
                              </Box>
                              <Box textAlign="right">
                                <IconButton sx={{ p: 0 }} onClick={()=>handleContactDelete(Address)}>
                                    <Icon icon='mdi:delete-outline' />
                                </IconButton>
                              </Box>
                            </Box>
                          </Card>
                        </Grid>
                      )

                    })}
                    </Grid>

                </Grid>
              </Grid>
            )}

            {pageModel == 'NewContact' && ( 
                <Grid container spacing={2}>
                    <Grid item xs={12} sx={{height: 'calc(100%)'}}>
                        <Grid container spacing={2}>
                            <TextField
                                fullWidth
                                size='small'
                                value={contactName}
                                onChange={(e) => setContactName(e.target.value)}
                                placeholder={t('Contact Name') as string}
                                sx={{ '& .MuiInputBase-root': { borderRadius: 2 } }}
                            />
                            <TextField
                                fullWidth
                                size='small'
                                value={contactAddress}
                                onChange={(e) => setContactAddress(e.target.value)}
                                placeholder={t('Contact Address') as string}
                                sx={{ mt: 3, '& .MuiInputBase-root': { borderRadius: 2 } }}
                            />
                            <Box sx={{width: '100%', mr: 3}}>
                                <Button sx={{mt: 8, mx: 2}} disabled={contactName != '' && contactAddress && contactAddress.length == 43 ? false : true} fullWidth variant='contained' onClick={()=>handleContactSave()}>
                                {t("Save")}
                                </Button>
                            </Box>
                        </Grid>
                    </Grid>
                        
                </Grid>
            )}

            {pageModel == 'SecurityPrivacy' && ( 
              <Grid container spacing={2}>
                <Grid item xs={12} sx={{height: 'calc(100%)'}}>
                    <Grid container spacing={2}>
                        <Grid item xs={12} sx={{ py: 1 }}>
                          <Card>
                            <Box sx={{ display: 'flex', alignItems: 'center', px: 2, py: 0.7}}>
                                <IconButton sx={{ p: 0 }} onClick={()=>handleClickTermsOfUseButton()}>
                                    <Icon icon='mdi:text-box-outline' fontSize={38} />
                                </IconButton>
                                <Box sx={{ cursor: 'pointer', ml: 2, display: 'flex', flexDirection: 'column', width: '100%' }} onClick={()=>handleClickTermsOfUseButton()}
                                    >
                                    <Typography sx={{ 
                                    color: 'text.primary',
                                    overflow: 'hidden',
                                    textOverflow: 'ellipsis',
                                    whiteSpace: 'nowrap',
                                    }}
                                    >
                                    {t('Terms of Use') as string}
                                    </Typography>
                                    <Box sx={{ display: 'flex'}}>
                                    <Typography variant='body2' sx={{ 
                                        color: `secondary.primary`, 
                                        overflow: 'hidden',
                                        textOverflow: 'ellipsis',
                                        whiteSpace: 'nowrap',
                                        flex: 1
                                    }}>
                                        {t('Terms of Use') as string}
                                    </Typography>
                                    </Box>
                                </Box>
                                <Box textAlign="right">
                                    <IconButton sx={{ p: 0 }} onClick={()=>handleClickTermsOfUseButton()}>
                                        <Icon icon='mdi:chevron-right' fontSize={30} />
                                    </IconButton>
                                </Box>
                            </Box>
                          </Card>
                        </Grid>
                        <Grid item xs={12} sx={{ py: 1 }}>
                          <Card>
                            <Box sx={{ display: 'flex', alignItems: 'center', px: 2, py: 0.7}}>
                                <IconButton sx={{ p: 0 }} onClick={()=>handleClickPrivacyPolicyButton()}>
                                    <Icon icon='iconoir:privacy-policy' fontSize={38} />
                                </IconButton>
                                <Box sx={{ cursor: 'pointer', ml: 2, display: 'flex', flexDirection: 'column', width: '100%' }} onClick={()=>handleClickPrivacyPolicyButton()}
                                    >
                                    <Typography sx={{ 
                                    color: 'text.primary',
                                    overflow: 'hidden',
                                    textOverflow: 'ellipsis',
                                    whiteSpace: 'nowrap',
                                    }}
                                    >
                                    {t('Privacy Policy') as string}
                                    </Typography>
                                    <Box sx={{ display: 'flex'}}>
                                    <Typography variant='body2' sx={{ 
                                        color: `secondary.primary`, 
                                        overflow: 'hidden',
                                        textOverflow: 'ellipsis',
                                        whiteSpace: 'nowrap',
                                        flex: 1
                                    }}>
                                        {t('Privacy Policy') as string}
                                    </Typography>
                                    </Box>
                                </Box>
                                <Box textAlign="right">
                                    <IconButton sx={{ p: 0 }} onClick={()=>handleClickPrivacyPolicyButton()}>
                                        <Icon icon='mdi:chevron-right' fontSize={30} />
                                    </IconButton>
                                </Box>
                            </Box>
                          </Card>
                        </Grid>
                        <Grid item xs={12} sx={{ py: 1 }}>
                          <Card>
                            <Box sx={{ display: 'flex', alignItems: 'center', px: 2, py: 0.7}}>
                                <IconButton sx={{ p: 0 }} onClick={()=>handleClickCheckPinCodeButton()}>
                                    <Icon icon='dashicons:privacy' fontSize={38} />
                                </IconButton>
                                <Box sx={{ cursor: 'pointer', ml: 2, display: 'flex', flexDirection: 'column', width: '100%' }} onClick={()=>handleClickCheckPinCodeButton()}
                                    >
                                    <Typography sx={{ 
                                    color: 'text.primary',
                                    overflow: 'hidden',
                                    textOverflow: 'ellipsis',
                                    whiteSpace: 'nowrap',
                                    }}
                                    >
                                    {t('Change Pin Code') as string}
                                    </Typography>
                                    <Box sx={{ display: 'flex'}}>
                                    <Typography variant='body2' sx={{ 
                                        color: `secondary.primary`, 
                                        overflow: 'hidden',
                                        textOverflow: 'ellipsis',
                                        whiteSpace: 'nowrap',
                                        flex: 1
                                    }}>
                                        {t('Change Pin Code') as string}
                                    </Typography>
                                    </Box>
                                </Box>
                                <Box textAlign="right">
                                    <IconButton sx={{ p: 0 }} onClick={()=>handleClickCheckPinCodeButton()}>
                                        <Icon icon='mdi:chevron-right' fontSize={30} />
                                    </IconButton>
                                </Box>
                            </Box>
                          </Card>
                        </Grid>
                    </Grid>
                </Grid>
              </Grid>
            )}

            {pageModel == 'Language' && (
                <Grid container spacing={2}>

                    <RadioGroup row value={'value'}  sx={{width: '100%'}} onClick={(e: any)=>e.target.value && handleSelectLanguage(e.target.value)}>
                        {LanguageArray.map((Language: any, index: number) => {

                            return (
                                <Grid item xs={12} sx={{ py: 1 }} key={index}>
                                    <Card sx={{ml: 2}}>
                                        <Box sx={{ display: 'flex', alignItems: 'center', px: 2, py: 0.7}}>
                                            <Box sx={{ cursor: 'pointer', display: 'flex', flexDirection: 'column', width: '100%', ml: 2 }} onClick={()=>handleSelectLanguage(Language.value)}>
                                                <Typography sx={{ color: 'text.primary', overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap', }} >
                                                    {Language.name}
                                                </Typography>
                                            </Box>
                                            <Box textAlign="right" sx={{m: 0, p: 0}}>
                                                <FormControlLabel value={Language.value} control={<Radio sx={{justifyContent: 'center', ml: 3, mr: 0}} checked={languageValue == Language.value}/>} label="" />
                                            </Box>
                                        </Box>
                                    </Card>
                                </Grid>
                            )

                        })}
                    </RadioGroup>

                </Grid>
            )}

            {pageModel == 'Theme' && (
                <Grid container spacing={2}>

                    <RadioGroup row value={'value'}  sx={{width: '100%'}} onClick={(e: any)=>e.target.value && handleSelectTheme(e.target.value)}>
                        {themeArray.map((Theme: any, index: number) => {

                            return (
                                <Grid item xs={12} sx={{ py: 1 }} key={index}>
                                    <Card sx={{ml: 2}}>
                                        <Box sx={{ display: 'flex', alignItems: 'center', px: 2, py: 0.7}}>
                                            <Box sx={{ cursor: 'pointer', display: 'flex', flexDirection: 'column', width: '100%', ml: 2 }}  onClick={()=>handleSelectTheme(Theme.value)}>
                                                <Typography sx={{ color: 'text.primary', overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap', }} >
                                                    {Theme.name}
                                                </Typography>
                                            </Box>
                                            <Box textAlign="right" sx={{m: 0, p: 0}}>
                                                <FormControlLabel value={Theme.value} control={<Radio sx={{justifyContent: 'center', ml: 3, mr: 0}} checked={themeValue == Theme.value}/>} label="" />
                                            </Box>
                                        </Box>
                                    </Card>
                                </Grid>
                            )

                        })}
                    </RadioGroup>

                </Grid>
            )}

            {pageModel == 'Currency' && (
                <Grid container spacing={2}>

                    <RadioGroup row value={'value'}  sx={{width: '100%'}} onClick={(e: any)=>e.target.value && handleSelectCurrency(e.target.value)}>
                        {currencyArray.map((Currency: any, index: number) => {

                            return (
                                <Grid item xs={12} sx={{ py: 1 }} key={index}>
                                    <Card sx={{ml: 2}}>
                                        <Box sx={{ display: 'flex', alignItems: 'center', px: 2, py: 0.7}}>
                                            <Box sx={{ cursor: 'pointer', display: 'flex', flexDirection: 'column', width: '100%', ml: 2 }} onClick={()=>handleSelectCurrency(Currency.value)} >
                                                <Typography sx={{ color: 'text.primary', overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap', }} >
                                                    {Currency.name}
                                                </Typography>
                                            </Box>
                                            <Box textAlign="right" sx={{m: 0, p: 0}}>
                                                <FormControlLabel value={Currency.value} control={<Radio sx={{justifyContent: 'center', ml: 3, mr: 0}} checked={currencyValue == Currency.value}/>} label="" />
                                            </Box>
                                        </Box>
                                    </Card>
                                </Grid>
                            )

                        })}
                    </RadioGroup>

                </Grid>
            )}

            {pageModel == 'Network' && (
                <Grid container spacing={2}>

                    <RadioGroup row value={'value'}  sx={{width: '100%'}} onClick={(e: any)=>e.target.value && handleSelectNetwork(e.target.value)}>
                        {networkArray.map((Network: any, index: number) => {

                            return (
                                <Grid item xs={12} sx={{ py: 1 }} key={index}>
                                    <Card sx={{ml: 2}}>
                                        <Box sx={{ display: 'flex', alignItems: 'center', px: 2, py: 0.7}}>
                                            <Box sx={{ cursor: 'pointer', display: 'flex', flexDirection: 'column', width: '100%', ml: 2 }} onClick={()=>handleSelectNetwork(Network.value)} >
                                                <Typography sx={{ color: 'text.primary', overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap', }} >
                                                    {Network.name}
                                                </Typography>
                                            </Box>
                                            <Box textAlign="right" sx={{m: 0, p: 0}}>
                                                <FormControlLabel value={Network.value} control={<Radio sx={{justifyContent: 'center', ml: 3, mr: 0}} checked={networkValue == Network.value}/>} label="" />
                                            </Box>
                                        </Box>
                                    </Card>
                                </Grid>
                            )

                        })}
                    </RadioGroup>

                </Grid>
            )}

            {pageModel == 'CreateToken' && (
                <Grid container spacing={2}>

                      <TextField
                        fullWidth
                        size='small'
                        value={tokenName}
                        placeholder={t('Token Name, eg. AOTest') as string}
                        sx={{ '& .MuiInputBase-root': { borderRadius: 5 }, mb: 3 }}
                        onChange={(e: any)=>{
                          setTokenName(e.target.value)
                        }}
                      />
                      <TextField
                        fullWidth
                        size='small'
                        value={tokenTicker}
                        placeholder={t('Token Ticker, eg. AOT') as string}
                        sx={{ '& .MuiInputBase-root': { borderRadius: 5 }, mb: 3 }}
                        onChange={(e: any)=>{
                          setTokenTicker(e.target.value)
                        }}
                      />
                      <TextField
                        fullWidth
                        size='small'
                        value={tokenTotalBalance}
                        placeholder={t('Total Balance, eg. 10000') as string}
                        sx={{ '& .MuiInputBase-root': { borderRadius: 5 }, mb: 3 }}
                        onChange={(e: any)=>{
                          setTokenTotalBalance(String(Number(e.target.value)))
                        }}
                      />
                      <TextField
                        fullWidth
                        size='small'
                        value={tokenLogo}
                        placeholder={t('Token Logo Hash, length 32 tx id on AR') as string}
                        sx={{ '& .MuiInputBase-root': { borderRadius: 5 }, mb: 3 }}
                        onChange={(e: any)=>{
                          setTokenLogo(e.target.value)
                        }}
                      />
                      <TextField
                        fullWidth
                        size='small'
                        value={tokenDenomination}
                        placeholder={t('Token Denomination, eg. 12') as string}
                        sx={{ '& .MuiInputBase-root': { borderRadius: 5 }, mb: 0 }}
                        onChange={(e: any)=>{
                          setTokenDenomination(e.target.value)
                        }}
                      />

                      <Box sx={{width: '100%', mx: 2, display: 'flex', justifyContent: 'center'}}>
                        <Button sx={{mt: 8}}  disabled={isDisabledButton} variant='contained' onClick={()=>handleCreateTokenButton()}>
                          {uploadingButton}
                        </Button>
                      </Box>

                      {createTokenData && createTokenData && (
                        <>
                        <Typography sx={{ 
                                      color: 'text.primary',
                                      overflow: 'hidden',
                                      textOverflow: 'ellipsis',
                                      whiteSpace: 'nowrap',
                                      mt: 5
                                    }}
                                    onClick={async ()=>{
                                      await Clipboard.write({
                                        string: createTokenData.TokenId
                                      });
                                      toast.success(t('Copied success') as string, { duration: 1000, position: 'top-center' })
                                    }}
                        >
                        TokenId: {createTokenData.TokenId}
                        </Typography>
                        <Typography sx={{ 
                          color: 'text.primary',
                          overflow: 'hidden',
                          textOverflow: 'ellipsis',
                          whiteSpace: 'nowrap',
                          width: '100%'
                        }}
                        >
                        Name: {createTokenData.TokenData.Name}
                        </Typography>
                        <Typography sx={{ 
                          color: 'text.primary',
                          overflow: 'hidden',
                          textOverflow: 'ellipsis',
                          whiteSpace: 'nowrap',
                          width: '100%'
                        }}
                        >
                        Ticker: {createTokenData.TokenData.Ticker}
                        </Typography>
                        <Typography sx={{ 
                          color: 'text.primary',
                          overflow: 'hidden',
                          textOverflow: 'ellipsis',
                          whiteSpace: 'nowrap',
                          width: '100%'
                        }}
                        >
                        TotalSupply: {createTokenData.TokenData.TotalSupply}
                        </Typography>
                        <Typography sx={{ 
                          color: 'text.primary',
                          overflow: 'hidden',
                          textOverflow: 'ellipsis',
                          whiteSpace: 'nowrap',
                          width: '100%'
                        }}
                        >
                        Version: {createTokenData.TokenData.Version}
                        </Typography>
                        <Typography sx={{ 
                          color: 'text.primary',
                          overflow: 'hidden',
                          textOverflow: 'ellipsis',
                          whiteSpace: 'nowrap',
                          width: '100%'
                        }}
                        >
                        Denomination: {createTokenData.TokenData.Denomination}
                        </Typography>
                        <Typography sx={{ 
                          color: 'text.primary',
                          overflow: 'hidden',
                          textOverflow: 'ellipsis',
                          whiteSpace: 'nowrap',
                          width: '100%'
                        }}
                        >
                        Release: {createTokenData.TokenData.Release}
                        </Typography>
                        </>
                      )}

                </Grid>
            )}

            {pageModel == 'SetPinCode' && ( 
              <Grid container spacing={2}>
                <Grid item xs={12} sx={{height: 'calc(100% - 104px)'}}>
                </Grid>
              </Grid>
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

        </ContentWrapper>
      </Box>
    </Fragment>
  )
}

export default Setting
