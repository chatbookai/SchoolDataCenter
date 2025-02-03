
import Typography from '@mui/material/Typography'

const MsgData = ({ data }: any) => {
  const dataJson = JSON.parse(data)

  return (
    <>
      <Typography sx={{
                    width: 'fit-content',
                    fontSize: '0.875rem',
                    p: theme => theme.spacing(0.5, 2, 0.5, 2),
                    ml: 1,
                    color: 'text.primary',
                  }}
                  >{dataJson.message}</Typography>
    </>
  )
}

export default MsgData
