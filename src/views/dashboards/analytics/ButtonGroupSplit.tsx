// ** React Imports
import { useRef, useState, Fragment, SyntheticEvent } from 'react'

// ** MUI Imports
import Grow from '@mui/material/Grow'
import Paper from '@mui/material/Paper'
import Button from '@mui/material/Button'
import Popper from '@mui/material/Popper'
import MenuItem from '@mui/material/MenuItem'
import MenuList from '@mui/material/MenuList'
import ButtonGroup from '@mui/material/ButtonGroup'
import ClickAwayListener from '@mui/material/ClickAwayListener'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

interface DataType {
    data: []
    toggleSetClassName: (arg0: string) => void
}
  
const ButtonGroupSplit = (props: DataType) => {

  const { data, toggleSetClassName } = props
  console.log("data",data)

  // ** States
  const [open, setOpen] = useState<boolean>(false)
  const [selectedIndex, setSelectedIndex] = useState<number>(0)

  // ** Ref
  const anchorRef = useRef<HTMLDivElement | null>(null)

  const handleClick = () => {
    console.info('You clicked',data[selectedIndex]['name'])
  }

  const handleMenuItemClick = (event: SyntheticEvent, index: number) => {
    setSelectedIndex(index)
    setOpen(false)
    toggleSetClassName(data[index]['name'])
  }

  const handleToggle = () => {
    setOpen(prevOpen => !prevOpen)
  }

  const handleClose = () => {
    setOpen(false)
  }

  return (
    <Fragment>
      <ButtonGroup variant='contained' ref={anchorRef} aria-label='split button'>
        <Button onClick={handleClick}>{data[selectedIndex]['name']}</Button>
        <Button
          sx={{ px: '0' }}
          aria-haspopup='menu'
          onClick={handleToggle}
          aria-label='select merge strategy'
          aria-expanded={open ? 'true' : undefined}
          aria-controls={open ? 'split-button-menu' : undefined}
        >
          <Icon icon='mdi:menu-down' />
        </Button>
      </ButtonGroup>
      <Popper open={open} anchorEl={anchorRef.current} role={undefined} transition disablePortal>
        {({ TransitionProps, placement }) => (
          <Grow
            {...TransitionProps}
            style={{ transformOrigin: placement === 'bottom' ? 'center top' : 'center bottom' }}
          >
            <Paper>
              <ClickAwayListener onClickAway={handleClose}>
                <MenuList id='split-button-menu'>
                  {data.map((option: any, index: number) => (
                    <MenuItem
                      key={index}
                      selected={index === selectedIndex}
                      onClick={event => handleMenuItemClick(event, index)}
                    >
                      {option['name']}
                    </MenuItem>
                  ))}
                </MenuList>
              </ClickAwayListener>
            </Paper>
          </Grow>
        )}
      </Popper>
    </Fragment>
  )
}

export default ButtonGroupSplit
