// ** React Import
import { useEffect } from 'react'

// ** Icon Imports
import Icon from '../../../../@core/components/icon'

// ** Third Party Import
import { useTranslation } from 'react-i18next'

// ** Custom Components Imports
import OptionsMenu from '../../../../@core/components/option-menu'

// ** Type Import
import { Settings } from '../../../../@core/context/settingsContext'

import { getChivesLanguage, setChivesLanguage } from '../../../../functions/ChivesWallets'

/*
import toast from 'react-hot-toast'
import Box from '@mui/material/Box'
import Avatar from '@mui/material/Avatar'
import IconButton from '@mui/material/IconButton'
import Typography from '@mui/material/Typography'
*/
//import { ReminderMsgAndStoreToLocal, GetAoConnectReminderProcessTxId } from '../../../../functions/AoConnect/MsgReminder'


interface Props {
  settings: Settings
  saveSettings: (values: Settings) => void
}

const LanguageDropdown = ({ settings, saveSettings }: Props) => {
  // ** Hook
  const { i18n } = useTranslation()

  // ** Vars
  const { layout } = settings

  const handleLangItemClick = (lang: 'en' | 'zh-CN' | 'zh-TW' | 'Ru' | 'Fr' | 'De' | 'Sp' | 'Kr' ) => {
    i18n.changeLanguage(lang)
    setChivesLanguage(lang)
  }

  // ** Change html `lang` attribute when changing locale
  useEffect(() => {
    const localLanguage = getChivesLanguage()
    if(localLanguage!=i18n.language && localLanguage!='') {
      i18n.changeLanguage(localLanguage)
      setChivesLanguage(localLanguage)
    }
    document.documentElement.setAttribute('lang', i18n.language)
  }, [i18n.language])


  return (
    <OptionsMenu
      icon={<Icon icon='mdi:translate' />}
      menuProps={{ sx: { '& .MuiMenu-paper': { mt: 4, minWidth: 130 } } }}
      iconButtonProps={{ color: 'inherit', sx: { ...(layout === 'vertical' ? { mr: 0.75 } : { mx: 0.75 }) } }}
      options={[
        {
          text: 'English',
          menuItemProps: {
            sx: { py: 2 },
            selected: i18n.language === 'en',
            onClick: () => {
              handleLangItemClick('en')
              saveSettings({ ...settings, direction: 'ltr' })
            }
          }
        },
        {
          text: 'Korean',
          menuItemProps: {
            sx: { py: 2 },
            selected: i18n.language === 'Kr',
            onClick: () => {
              handleLangItemClick('Kr')
              saveSettings({ ...settings, direction: 'ltr' })
            }
          }
        },
        {
          text: 'Russia',
          menuItemProps: {
            sx: { py: 2 },
            selected: i18n.language === 'Ru',
            onClick: () => {
              handleLangItemClick('Ru')
              saveSettings({ ...settings, direction: 'ltr' })
            }
          }
        },
        {
          text: '简体中文',
          menuItemProps: {
            sx: { py: 2 },
            selected: i18n.language === 'zh-CN',
            onClick: () => {
              handleLangItemClick('zh-CN')
              saveSettings({ ...settings, direction: 'ltr' })
            }
          }
        }
      ]}
    />
  )
}

export default LanguageDropdown
