// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'
import Grid from '@mui/material/Grid'
import Typography from '@mui/material/Typography'
import { useTranslation } from 'react-i18next'
import toast from 'react-hot-toast'

import TextField from '@mui/material/TextField'

const KeySetting = () => {
  
  const [apiKey, setApiKey] = useState<string>("")

  const { t } = useTranslation()

  const handleSaveApiKey = async (e: any) => {
    setApiKey( e.target.value )
    toast.success('成功设置Api Key')
  }

  return (
    <Grid container spacing={6}>
      <Grid item xs={12}>
        <Grid container spacing={6}>
            <Grid item xs={6} sx={{pt: 4, pl: 2}}>
                <Box sx={{ mb: 4, display: 'flex', alignItems: 'center' }}>
                    <Typography variant='h6' sx={{ fontWeight: 400, lineHeight: 'normal' }} >
                        设置您的Api Key
                    </Typography>
                </Box>
                <TextField
                    fullWidth
                    size="small"
                    value={apiKey}
                    sx={{ mb: 4, resize: 'both', '& .MuiInputBase-input': { fontSize: '0.875rem' } }}
                    placeholder={t('请输入您的API KEY') as string}
                    onChange={handleSaveApiKey}
                    />
                <Box sx={{ mb: 4, display: 'flex', alignItems: 'center' }}>
                    <Typography variant='body2' >
                        如果您没有Api Key，请加QQ群 138595160，获得Api Key.
                        每个学校用户，经过联系以后，可以获得50次AI生成心理测评报告的机会。
                        50次AI生成心理测评报告的额度用完以后，学校可以继续使用本系统，唯一影响的是生成学生心理测评报告的内容里面,不会有第四项AI生成心理测评报告的内容，其它部分不受影响。
                    </Typography>
                </Box>
            </Grid>
        </Grid>
      </Grid>
    </Grid>
  )
}

export default KeySetting
