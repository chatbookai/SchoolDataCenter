'use client'

// Next Imports
import { redirect, usePathname } from 'next/navigation'

// Config Imports
import { i18n } from '@/configs/i18n'

// Util Imports
import { ensurePrefix } from '@/utils/string'

const LangRedirect = ({ redirectPrefix }: { redirectPrefix: string }) => {
  const pathname = usePathname()

  let redirectUrl = `/${i18n.defaultLocale}${pathname}`

  // ℹ️ This is only required for 6 demos
  redirectUrl = ensurePrefix(redirectUrl, redirectPrefix)

  redirect(redirectUrl)
}

export default LangRedirect
