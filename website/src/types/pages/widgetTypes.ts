// MUI Imports
import type { AvatarProps } from '@mui/material'

// Type Imports
import type { ThemeColor } from '@core/types'
import type { OptionsMenuType } from '@core/components/option-menu/types'
import type { CustomAvatarProps } from '@core/components/mui/Avatar'

export type CardStatsHorizontalWithAvatarProps = {
  stats: string
  title: string
  avatarIcon: string
  avatarColor?: ThemeColor
  avatarVariant?: AvatarProps['variant']
  avatarSkin?: CustomAvatarProps['skin']
  avatarIconSize?: number
  avatarSize?: number
}

export type CardStatsHorizontalWithBorderProps = {
  title: string
  stats: number
  trendNumber: number
  avatarIcon: string
  color?: ThemeColor
}

export type CardStatsCustomerStatsProps = {
  title: string
  avatarIcon: string
  color?: ThemeColor
  description: string
} & (
  | {
      stats?: string
      content?: string
      chipLabel?: never
    }
  | {
      chipLabel?: string
      stats?: never
      content?: never
    }
)

export type CardStatsType = {
  statsHorizontalWithAvatar: CardStatsHorizontalWithAvatarProps[]
  statsHorizontalWithBorder: CardStatsHorizontalWithBorderProps[]
  customerStats: CardStatsCustomerStatsProps[]
  statsVertical: CardStatsVerticalProps[]
  statsCharacter: CardStatsCharacterProps[]
  statsHorizontal: CardStatsHorizontalProps[]
}

export type CardStatsHorizontalProps = {
  title: string
  stats: string
  icon: string
  color?: ThemeColor
  trendNumber: string
  trend?: string
}

export type CardStatsVerticalProps = {
  title: string
  stats: string
  avatarIcon: string
  subtitle: string
  avatarColor?: ThemeColor
  trendNumber: string
  trend?: 'positive' | 'negative'
  avatarSkin?: CustomAvatarProps['skin']
  avatarSize?: number
  moreOptions?: OptionsMenuType
}

export type CardStatsCharacterProps = {
  src: string
  title: string
  stats: string
  chipText: string
  trendNumber: string
  chipColor?: ThemeColor
  trend?: 'positive' | 'negative'
}
