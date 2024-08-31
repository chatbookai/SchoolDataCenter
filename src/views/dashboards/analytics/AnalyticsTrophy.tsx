// ** MUI Imports
import Card from '@mui/material/Card'
import Button from '@mui/material/Button'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'
import { styled } from '@mui/material/styles'
import { useRouter } from 'next/router'
import TextField from '@mui/material/TextField'
import Autocomplete from '@mui/material/Autocomplete'

// Styled component for the trophy image
const TrophyImg = styled('img')({
  right: 36,
  bottom: 20,
  height: 98,
  position: 'absolute'
})

interface DataType {
  className: string
  data: {[key:string]:any}
  toggleSetClassName: (arg0: string) => void
}

const AnalyticsTrophy = (props: DataType) => {

  const { className, data, toggleSetClassName } = props

  const router = useRouter();

  return (
    <Card sx={{ position: 'relative' }}>
      <CardContent style={{ overflow: 'auto' }}>
        <Typography variant='h6'>{data.Welcome}</Typography>
        <Typography variant='body2' sx={{ letterSpacing: '0.25px' }}>
        {data.SubTitle}
        </Typography>
        <Typography variant='h5' sx={{ my: 3.5, color: 'primary.main' }}>
          {data.TotalScore}
        </Typography>
        {data.TopRightOptions ? 
          <Autocomplete
            autoSelect
            size="small"
            sx={{ width: 200 }}
            options={data.TopRightOptions}
            id='autocomplete-autoSelect'
            getOptionLabel={option => option.name || ''}
            renderInput={params => <TextField {...params} label='请选择' />}
            onChange={(event: any, newValue: any) => {
              if(newValue && newValue!=undefined && newValue.code) {
                toggleSetClassName(newValue.code)
              }
            }}
            isOptionEqualToValue={(option, value) => option.code === value.code}
            value={data.TopRightOptions.find((option: { code: string }) => option.code == className)}
          />
        :
          <Button size='small' variant='contained' onClick={() => router.push(data.ViewButton.url)}>
            {data.ViewButton.name}
          </Button>
        }
        <TrophyImg alt='trophy' src='/images/misc/trophy.png' />
      </CardContent>
    </Card>
  )
}

export default AnalyticsTrophy
