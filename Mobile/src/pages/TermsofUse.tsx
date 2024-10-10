import React, { ReactNode } from 'react';

import BlankLayout from 'src/@core/layouts/BlankLayout'

import TermsofUseModel from '../views/Setting/TermsofUse'

const TermsofUse = () => {

  return <TermsofUseModel />
}

TermsofUse.getLayout = (page: ReactNode) => <BlankLayout>{page}</BlankLayout>

export default TermsofUse;
