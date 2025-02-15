// MUI Imports
import Card from '@mui/material/Card'
import TextField from '@mui/material/TextField'
import Typography from '@mui/material/Typography'
import { useTheme } from '@mui/material/styles'

// Third-party Imports
import classnames from 'classnames'

// Type Imports
import type { Mode } from '@core/types'

// Component Imports
import CustomIconButton from '@core/components/mui/IconButton'

// Hook Imports
import { useImageVariant } from '@core/hooks/useImageVariant'

type Props = {
  mode: Mode
  searchValue: string
  setSearchValue: (value: string) => void
}

const MyCourseHeader = (props: Props) => {
  // Props
  const { mode, searchValue, setSearchValue } = props

  // Vars
  const lightIllustration = '/images/academy/hand-with-bulb-light.png'
  const darkIllustration = '/images/academy/hand-with-bulb-dark.png'

  // Hooks
  const theme = useTheme()
  const leftIllustration = useImageVariant(mode, lightIllustration, darkIllustration)

  return (
    <Card className='relative flex justify-center'>
      <img src={leftIllustration} className='max-md:hidden absolute max-is-[100px] top-12 start-12' />
      <div className='flex flex-col items-center gap-4 max-md:pli-5 plb-12 md:is-1/2'>
        <Typography variant='h4' className='text-center md:is-3/4'>
          Education, talents, and career opportunities. <span className='text-primary'>All in one place.</span>
        </Typography>
        <Typography className='text-center'>
          Grow your skill with the most reliable online courses and certifications in marketing, information technology,
          programming, and data science.
        </Typography>
        <div className='flex items-center gap-4 max-sm:is-full'>
          <TextField
            placeholder='Find your course'
            size='small'
            value={searchValue}
            onChange={e => setSearchValue(e.target.value)}
            className='sm:is-[350px] max-sm:flex-1'
          />
          <CustomIconButton variant='contained' color='primary'>
            <i className='ri-search-2-line' />
          </CustomIconButton>
        </div>
      </div>
      <img
        src='/images/academy/9.png'
        className={classnames('max-md:hidden absolute max-bs-[180px] bottom-0 end-0', {
          'scale-x-[-1]': theme.direction === 'rtl'
        })}
      />
    </Card>
  )
}

export default MyCourseHeader
