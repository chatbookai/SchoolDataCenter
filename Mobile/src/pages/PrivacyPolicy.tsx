import React, { ReactNode } from 'react';

import BlankLayout from 'src/@core/layouts/BlankLayout'

import PrivacyPolicyModel from '../views/Setting/PrivacyPolicy'

const PrivacyPolicy = () => {

  return <PrivacyPolicyModel />
}

PrivacyPolicy.getLayout = (page: ReactNode) => <BlankLayout>{page}</BlankLayout>

export default PrivacyPolicy;
