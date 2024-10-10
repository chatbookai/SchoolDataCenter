
// ** MUI Imports
import { styled } from '@mui/material/styles'
import Box, { BoxProps } from '@mui/material/Box'

// ** Type Import
import { LayoutProps } from '../../@core/layouts/types'

const VerticalLayoutWrapper = styled('div')({
  height: '100%',
  display: 'flex'
})

const MainContentWrapper = styled(Box)<BoxProps>({
  flexGrow: 1,
  minWidth: 0,
  display: 'flex',
  minHeight: '100vh',
  flexDirection: 'column'
})

const VerticalLayout = (props: LayoutProps) => {
  // ** Props
  const { children, contentHeightFixed } = props

  return (
    <>
      <VerticalLayoutWrapper className='layout-wrapper'>
        <MainContentWrapper
          className='layout-content-wrapper'
          sx={{ ...(contentHeightFixed && { maxHeight: '100vh' }) }}
        >
          {children}
        </MainContentWrapper>
      </VerticalLayoutWrapper>

    </>
  )
}

export default VerticalLayout
