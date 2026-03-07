interface LoginCtaBarProps {
  loginUrl: string;
}

export default function LoginCtaBar({ loginUrl }: LoginCtaBarProps) {
  return (
    <div className="action-bar">
      <a href={loginUrl} className="action-btn" rel="noopener noreferrer nofollow" target="_blank">
        GİRİŞ İÇİN TIKLAYINIZ
      </a>
    </div>
  );
}
