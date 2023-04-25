import { LOCAL_AUTH_KEY } from '@/auth/local/local.strategy';
import { Injectable } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';

@Injectable()
export class LocalAuthGuard extends AuthGuard(LOCAL_AUTH_KEY) {}
