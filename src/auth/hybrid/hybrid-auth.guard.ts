import { Injectable } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';

@Injectable()
export class HybridAuthGuard extends AuthGuard(['jwt', 'token']) {}
