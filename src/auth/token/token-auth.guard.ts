import { TOKEN_AUTH_KEY } from '@/auth/token/token.strategy';
import { Injectable } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';

@Injectable()
export class TokenAuthGuard extends AuthGuard(TOKEN_AUTH_KEY) {}