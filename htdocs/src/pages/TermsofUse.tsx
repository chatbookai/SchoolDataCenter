import React, { ReactNode } from 'react';
import { authConfig } from 'src/configs/auth'

import BlankLayout from 'src/@core/layouts/BlankLayout'

const TermsofUse = () => {
  return (
    <div style={{margin: '20px'}}>
      <div className="d-export-content">
        <h1 id="Chatbook-open-platform-terms-of-service">
          <span>{authConfig.AppName} 使用条款</span>
        </h1>
        <p><span>亲爱的用户，欢迎使用 {authConfig.AppName}！</span></p>

        <p>
          <span>
            {authConfig.AppName} 产品和服务的所有权和运营权归 {authConfig.AppName} 所有。在使用服务之前，请务必仔细阅读并理解本《{authConfig.AppName} 使用条款》（以下简称“本条款”）以及本平台的其他相关条款、政策或指南。当您使用服务的特定功能时，可能会有该特定功能的单独条款、相关业务规则等（“特定条款”）。如果本条款与特定条款之间存在任何冲突，则以特定条款的规定为准。<strong>上述所有条款和规则构成本条款的组成部分（统称为“所有条款”），与本条款正文具有同等法律效力。</strong>
          </span>
        </p>

        <p>
          <span>
            <strong>
              其中，《{authConfig.AppName} 开放平台服务条款》特别适用于您使用本平台提供的应用程序编程接口（API）或其他开发者工具和开放平台服务。
            </strong>
          </span>
        </p>

        <p>
          <span>
            我们特别提醒您在使用服务前仔细阅读（未满18周岁的未成年人应在其法定监护人陪同下阅读）并充分理解所有条款，特别是以粗体显示的条款。当您通过在线页面点击、勾选或实际使用我们的服务等方式同意本条款时，即表示您与我们已就所有条款达成协议，您已接受所有条款及其适用条件，并同意受所有条款的约束。如果您不同意本条款的任何部分，或无法准确理解我们对任何条款的解释，请点击不同意或停止使用我们的服务。
          </span>
        </p>


        <h2 id="1services">
          <span>1.</span>
          <strong><span>服务</span></strong>
        </h2>

        <p>
          <span>
            1.1 {authConfig.AppName} 的产品和服务包括通过网站、应用程序（可能包括不同版本）、第三方网站和应用程序的软件开发工具包（SDK）、应用程序编程接口（API）以及随着技术发展而出现的创新形式提供给您的产品和服务。
          </span>
        </p>

        <p>
          <span>
            1.2 您可以通过计算机和手机等终端以网页形式使用服务，具体细节以 {authConfig.AppName} 提供的内容为准。此外，{authConfig.AppName} 将持续丰富您使用服务的终端和形式。您理解并同意，{authConfig.AppName} 保留增加新服务或变更、暂停或终止上述服务的权利。
          </span>
        </p>

        <p>
          <span>
            1.3 {authConfig.AppName} 将采取必要措施（不低于行业惯例）确保服务的网络安全和稳定运行。我们还将努力提升和改进技术，以确保更好的用户体验。如果您对我们的服务有任何疑问或反馈，可以通过第12条所述方式联系我们。
          </span>
        </p>

        <h2 id="2account">
          <span>2.</span>
          <strong><span>账户</span></strong>
        </h2>
        <p>
          <span>2.1 当您注册和使用账户时，您必须承诺并保证：</span>
        </p>
        <ul>
          <li>
            <span>您对注册信息的真实性、合法性、有效性承担全部责任，及时更新注册信息，不得以他人名义注册账户或使用本服务。</span>
          </li>
          <li>
            <span>安全保管您的账户和密码，并对该账户下的所有活动承担法律责任。</span>
          </li>
          <li>
            <span>不得恶意注册账户，包括但不限于频繁或批量注册。</span>
          </li>
          <li>
            <span>不得以任何形式转让、出借、出租或提供您的账户给他人。</span>
          </li>
        </ul>

        <p>
          <span>
            2.2 如果我们发现您存在违法或违规行为，如通过虚假信息获取账户注册，或违反本协议，我们有权采取要求限期改正、暂停使用、无通知关闭账户等措施。如果用户发现其账户被他人未经授权使用，应立即通知我们，我们将提供最大程度的配合和处理。
          </span>
        </p>

        <p>
          <span>
            2.3 如果您丢失账户、忘记密码或泄露验证码，您可以及时按照程序申诉找回。我们特别提醒您妥善保管您的账户、密码和验证码。使用后应安全退出。您将承担因账户或密码泄露造成的全部责任。
          </span>
        </p>

        <h2 id="3requirements-and-restrictions"> <span>3.</span> <strong><span>要求与限制</span></strong> </h2>

        <p>
          <span>
            3.1 您充分理解并同意，在本协议下，我们授予您一项可撤销的、不可转让的、非独占的、非商业性的合法使用本产品和相关服务的权利。本协议未明确授权的所有其他权利均由{authConfig.AppName}保留，在行使这些权利之前，您必须获得{authConfig.AppName}的书面许可。此外，{authConfig.AppName}未行使这些权利并不构成对这些权利的放弃。如果您发布或传播由本服务生成的输出，您必须：（1）主动核实输出内容的真实性和准确性，以避免传播虚假信息；（2）明确标示输出内容由人工智能生成，以提醒公众内容的合成性质；（3）避免发布和传播任何违反本协议使用规范的输出内容。
          </span>
        </p>

        <p>
          <span>
            3.2 在使用{authConfig.AppName}提供的服务时，用户应遵守本协议，并遵循自愿、平等、公平、诚信的原则。用户不得利用该服务侵犯他人的合法权益或谋取不正当利益，也不得扰乱互联网平台的正常秩序。
          </span>
        </p>

        <p>
          <span>
            3.3 为履行法律和合规要求，{authConfig.AppName}有权使用技术或人工手段审查用户使用本服务的行为和信息，包括但不限于审查输入和输出、建立风险过滤机制、创建非法内容特征的数据库等。
          </span>
        </p>

        <p>
          <span>
            <strong>3.4 您不得使用本服务生成、表达或推广以下内容或聊天机器人：</strong>
          </span>
        </p>
        <p>
          <span>(1) 含有仇恨、诽谤、冒犯、辱骂、侵权或粗俗的内容；</span>
        </p>
        <p>
          <span>(2) 故意设计用于挑衅或对抗他人，或进行欺凌或骚扰他人的内容；</span>
        </p>
        <p>
          <span>(3) 可能骚扰、恐吓、威胁、伤害、惊吓、困扰、尴尬或惹恼他人的内容；</span>
        </p>
        <p>
          <span>(4) 基于种族、性别、性取向、宗教、国籍、残疾或年龄等进行歧视的内容；</span>
        </p>
        <p>
          <span>(5) 色情、淫秽或性暗示的内容（例如性聊天机器人）；</span>
        </p>
        <p>
          <span>(6) 促进、煽动或美化暴力或恐怖主义/极端主义内容；</span>
        </p>
        <p>
          <span>(7) 利用、伤害或试图利用或伤害未成年人，或向未成年人暴露此类内容；</span>
        </p>
        <p>
          <span>(8) 专门设计用于吸引或呈现18岁以下人士形象的内容；</span>
        </p>
        <p>
          <span>(9) 构成、鼓励或提供犯罪指导的内容；或</span>
        </p>
        <p>
          <span>(10) 冒充或设计用于冒充名人、公众人物或除您自己以外的其他人，而未明确标示内容或聊天机器人为“非官方”或“恶搞”，除非您已获得该人的明确同意。</span>
        </p>

        <p>
          <strong><span>3.5 在使用我们的服务时，您应遵守任何适用法律法规的要求，不得：</span></strong>
        </p>
        <p>
          <span>(1) 从事涉及网络入侵的违法行为，例如：使用未经授权的数据或访问未经授权的服务器/账户；伪造TCP/IP数据包名称或部分名称；未经许可尝试探测、扫描或测试软件系统或网络的漏洞。</span>
        </p>
        <p>
          <span>(2) 从事扰乱或破坏网络正常运行的活动，例如：故意生成、传播恶意程序或病毒；未经许可进入公共计算机网络或其他人的计算机系统，删除、修改、增加存储信息等。</span>
        </p>
        <p>
          <span>(3) 从事窃取网络数据的活动，例如：反向工程、反向组装、反向编译、翻译或以任何方式尝试发现软件的源代码、模型、算法、系统源代码或底层组件；捕获、复制本服务的任何内容，包括但不限于使用任何机器人、蜘蛛或其他自动设置，设置镜像。</span>
        </p>
        <p>
          <span>(4) 提供专门用于网络入侵、破坏网络运行和防护措施、窃取网络数据等有害活动的程序、工具，或明知支持他人从事与网络安全相关的有害活动，通过提供技术支持、广告、推广、支付结算等。</span>
        </p>
        <p>
          <span>(5) 从事可能导致物理损害的高风险活动，例如军事和战争、武器、爆炸物或危险材料的发展、关键基础设施（如交通、能源）的管理或运营、受控物质或服务的创建或分发等。</span>
        </p>
        <p>
          <span>(6) 从事侵犯个人合法权益、损害个人身心健康的活动，例如侵犯他人的个人权利、财产权和个人信息权利。</span>
        </p>
        <p>
          <span>(7) 侵犯知识产权、商业秘密及其他违反商业道德的行为。</span>
        </p>
        <p>
          <span>(8) 利用算法、数据、平台等优势，实施垄断和不正当竞争行为。</span>
        </p>
        <p>
          <span>(9) 从事扰乱社会秩序的活动，例如赌博、色情、毒品、传播虚假信息等。</span>
        </p>
        <p>
          <span>(10) 使用本服务进行欺诈、误导或欺骗活动，包括但不限于将聊天机器人生成的答案冒充为人类生成、抄袭或学术不端、传播虚假信息、诈骗或钓鱼。</span>
        </p>
        <p>
          <span>(11) 使用本服务开发与本服务竞争的产品和服务（除非此类限制违反相关法律法规）。</span>
        </p>
        <p>
          <span>(12) 未经{authConfig.AppName}同意或无合法正当理由，移除或篡改与本服务相关的深度合成内容的AI生成标识符或显著标识符。</span>
        </p>
        <p>
          <span>(13) 以导致{authConfig.AppName}计算机系统或设施不合理负载的方式获取输出，或从事可能导致此类情况的活动。</span>
        </p>
        <p>
          <span>(14) 未经{authConfig.AppName}授权，复制、转让、出租、出借、出售或提供分许可或再许可本服务的全部或任何部分。</span>
        </p>

        <p>
          <span>3.6 您应建立组织和技术措施，包括但不限于用户管理、数据安全、监控、预警和应急处置，以确保您的系统、网络、信息和数据的完整性、保密性和可用性，防止数据安全、舆论或产品或服务滥用和滥用的风险。</span>
        </p>

        <h2 id="4intellectual-property"> <span>4. 知识产权</span> </h2>

        <p>
          <span>
            4.1 除以下条款另有规定外，本服务中由{authConfig.AppName}提供的内容（包括但不限于软件、技术、程序、网页、文字、图片、图形、音频、视频、图表、版面设计、电子文档等）的知识产权及相关权益均属于{authConfig.AppName}。{authConfig.AppName}依赖提供服务的软件的著作权、专利权等知识产权归{authConfig.AppName}、其关联实体或各自的权利人所有。未经我们许可，任何人不得使用（包括但不限于通过任何机器人、“蜘蛛”或类似程序或设备监控、复制、传播、展示、镜像、上传、下载相关服务内容）。
          </span>
        </p>

        <p>
          <span>
            4.2 您对提交给本服务的所有输入及其相应的输出负责。通过向本服务提交输入，您表示并保证您拥有所有必要的权利、许可和权限，以便我们根据本协议处理这些输入。您还表示并保证，您向我们提交输入及其相应的输出不会违反本协议，或适用于这些输入和输出的任何法律或法规。在您和{authConfig.AppName}之间，并且在适用法律允许的范围内，您保留您提交的输入中的任何权利、所有权和利益。
          </span>
        </p>

        <p>
          <span>
            4.3 通过使用我们的服务，您在此授予我们在当地法律允许的范围内，无条件、不可撤销、非独占、免版税、可再许可、可转让、永久和全球性的许可，以复制、使用、修改您与提供服务相关的输入和输出。
          </span>
        </p>

        <p>
          <span>
            4.4 未经我们许可，您或您的最终用户不得以任何方式使用与本服务相关的任何商标、服务标志、商号、域名、网站名称、公司标志（LOGO）、URL或其他突出的品牌特征，包括但不限于“{authConfig.AppName}”等，无论是单独使用还是组合使用。您不得以任何方式显示、使用或申请注册与上述条款相关的商标、域名等，并且不得进行明示或暗示表明有权展示、使用或以其他方式处理这些标识符的行为。
          </span>
        </p>


        <h2 id="5privacy">
          <span>5. 隐私</span>
        </h2>

        <p>
          <span>
            5.1 我们尊重并保护您及所有服务用户的个人信息和隐私。我们收集您在使用我们的产品和服务时主动输入和提供的信息，以及服务生成的信息。有关我们如何收集、保护和使用个人信息的详细规则，请仔细阅读隐私政策。
          </span>
        </p>

        <p>
          <span>
            5.2 为了履行法律法规规定的要求或提供本协议规定的服务，并在安全加密技术处理、严格去标识化处理和不可逆识别特定个人的前提下，我们可能会在最小范围内使用输入和输出，以提供、维护、运营、开发或改进服务或支持服务的底层技术。请注意，除非我们获得您的单独同意，否则我们不会将上述数据和内容用于任何与服务无关的目的。
          </span>
        </p>


        <h2 id="6Complaints-and-Feedback">
          <span>6. 投诉与反馈</span>
        </h2>

        <p>
          <span>
            如果您认为我们的服务侵犯了您的知识产权或其他权利，或者您发现任何违法、虚假信息或违反本协议的行为，或者您对我们的服务有任何意见和建议，您可以通过产品界面提交。我们认真对待您的意见，并将采取相应的法律行动。
          </span>
        </p>


        <h2 id="7Exclusion-of-Warranties">
          <span>7. 免责声明</span>
        </h2>

        <p>
          <span>
            7.1 本协议中的任何内容均不影响您作为消费者在法律上始终享有的任何无法通过合同协议更改或放弃的法定权利。
          </span>
        </p>

        <p>
          <span>
            7.2 本服务按“现状”和“可用”的基础提供，我们不对您就服务作出任何明示或暗示的保证，包括但不限于有关满意质量、适合特定目的或与描述相符的默示条款。特别是，我们不对您作出以下陈述或保证：
          </span>
        </p>
        <p>
          <span>(1) 您使用本服务将满足您的需求；</span>
        </p>
        <p>
          <span>(2) 您使用本服务或其任何部分将是无中断、及时、安全或无错误的；</span>
        </p>
        <p>
          <span>(3) 您使用本服务获得的任何输出或其他信息将是准确、最新、可靠、无侵权或安全的；</span>
        </p>
        <p>
          <span>(4) 本服务的操作或功能中的缺陷将被纠正；或</span>
        </p>
        <p>
          <span>(5) 有关第三方产品。</span>
        </p>

        <p>
          <span>
            7.3 我们可能会出于业务和运营原因，随时更改、暂停、撤回或限制我们平台或服务的全部或任何部分的可用性，恕不另行通知。
          </span>
        </p>

        <p>
          <span>
            您同意赔偿、辩护并使我们及其关联公司和许可方（如有）免受任何因您或您账户的任何用户违反本协议、您违反所有适用法律和法规或第三方权利、您的欺诈或其他非法行为，或您的故意不当行为或重大过失而导致的第三方责任、损害和成本（包括合理的律师费），在适用法律允许的范围内。
          </span>
        </p>

        <h2 id="8Indemnification">
          <span>8. 赔偿</span>
        </h2>

        <p>
          <span>
            您同意赔偿、辩护并使我们及其关联公司和许可方（如有）免受任何因您或您账户的任何用户违反本协议、您违反所有适用法律和法规或第三方权利、您的欺诈或其他非法行为，或您的故意不当行为或重大过失而导致的第三方责任、损害和成本（包括合理的律师费），在适用法律允许的范围内。
          </span>
        </p>


        <h2 id="9Limitation-of-Liability">
          <span>9. 责任限制</span>
        </h2>

        <p>
          <span>
            9.1 当本服务提供的内容包括插件、第三方网站等提供的内容时，{authConfig.AppName}不对该内容的效果负责，也不保证通过这些方式获得的任何内容、产品、服务或其他材料的合法性、真实性或安全性。您应仔细评估该内容的合法性、准确性、真实性、适用性、完整性和安全性，并采取谨慎的预防措施。如果您对该内容的合法性、准确性、真实性、实用性、完整性和安全性不确定，建议咨询专业人士。在法律允许的范围内，{authConfig.AppName}不对您因使用任何第三方信息或链接而导致的任何个人或财产损失承担责任，包括但不限于因下载计算机病毒、名誉或商誉诽谤、版权或知识产权侵权等相关的损失。
          </span>
        </p>

        <p>
          <span>
            9.2 针对您违反本协议或其他服务条款的行为，{authConfig.AppName}有权独立判断并采取措施，包括但不限于发出警告、设定整改期限、限制账户功能、暂停使用、关闭账户、禁止重新注册、删除相关内容等，无需事先通知。我们有权公布采取行动的结果，并根据实际情况决定是否恢复使用。对于涉嫌违反法律法规或涉及违法行为的行为，将保留相关记录，并依法向主管部门报告，配合其调查。您应独自承担由此产生的任何法律责任、索赔、要求或损失，并赔偿我们因此遭受的任何损失，包括诉讼费、仲裁费、律师费、公证费、公告费、评估费、差旅费、调查取证费、赔偿金、违约金、和解费用及行政罚款等。
          </span>
        </p>

        <p>
          <span>
            9.3 除非另有约定，任何一方均不承担附带、间接、惩罚性、特殊或间接的损失或损害，包括但不限于利润或商誉的损失，无论这些损失或损害是如何产生的，或基于何种责任理论，以及无论基于违约、侵权、赔偿或其他任何法律依据提起的诉讼，即使被告知可能发生此类损失。
          </span>
        </p>


        <h2 id="10Export-Control-and-Sanctions">
          <span>10. 出口管制和制裁</span>
        </h2>

        <p>
          <span>
            您理解，您使用本服务、向本服务提供输入并获取输出可能受所有适用的出口管制和制裁法律和法规（统称为“出口管制和制裁法律”）的约束。您承认，您和您的最终用户对遵守与访问和使用本服务相关的所有适用出口管制和制裁法律负有全部责任。您表示并保证，本服务不得用于或为以下任何国家或地区、任何适用出口管制和制裁法律下的任何受限方名单上的任何方（这将禁止您使用本服务）的利益而出口、再出口或转让：（a）受全面制裁的国家或地区；（b）任何适用出口管制和制裁法律下的任何受限方名单上的任何方。本服务不得用于任何适用出口管制和制裁法律禁止的最终用途，您和您的最终用户的输入不得包括需要许可证才能发布或出口的材料或信息。
          </span>
        </p>


        <h2 id="11Governing-Law-and-Jurisdiction">
          <span>11. 适用法律和管辖权</span>
        </h2>

        <p>
          <span>
            11.1 本协议的订立、执行、解释及争议的解决均适用中华人民共和国大陆地区的法律。
          </span>
        </p>

        <p>
          <span>
            11.2 因本协议的签署、履行或解释而产生的或与之相关的任何争议，双方应尽力通过友好协商解决。如协商不成，任何一方均有权向杭州{authConfig.AppName}人工智能有限公司注册地有管辖权的法院提起诉讼。
          </span>
        </p>


        <h2 id="12Miscellaneous">
          <span>12. 其他</span>
        </h2>

        <p>
          <span>
            12.1 您对本协议的接受包括接受{authConfig.AppName}随时对任何条款进行的任何修改。我们有权随时修改本协议，修改后的协议将通过官方网站等适当渠道公布。一经公布，即取代原协议。请在官方网站上查阅本协议的最新版本。如果您不接受修改后的条款，请立即停止使用本服务。您继续使用本服务将被视为您接受修改后的条款。
          </span>
        </p>

        <p>
          <span>
            12.2 如果您发现任何违反法律法规或本协议的行为，或对本协议或本服务有任何意见或建议，您可以随时联系我们。
          </span>
        </p>

    </div>
  </div>
  );
};

TermsofUse.getLayout = (page: ReactNode) => <BlankLayout>{page}</BlankLayout>

TermsofUse.guestGuard = true

export default TermsofUse;
