import { PassportStrategy } from '@/auth/auth.constants';
import { ExecutionContext, Injectable } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';
import { Request } from 'express';
import { Observable } from 'rxjs';

@Injectable()
export class SessionAuthGuard extends AuthGuard(PassportStrategy.SESSION) {
	canActivate(
		context: ExecutionContext,
	): boolean | Promise<boolean> | Observable<boolean> {
		const request: Request = context.switchToHttp().getRequest();

		if (request.isAuthenticated()) {
			return true;
		}

		return false;
	}
}
