interface LoginCtaBarProps {
  loginUrl: string;
}

export default function LoginCtaBar({ loginUrl }: LoginCtaBarProps) {
  return (
    <div className="login-cta-bar">
      <a href={loginUrl} className="login-cta-btn" rel="noopener noreferrer nofollow" target="_blank">
        GİRİŞ İÇİN TIKLAYINIZ
      </a>
    </div>
  );
}
