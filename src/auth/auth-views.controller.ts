import { AuthJwtPayload } from '@/auth/decorators/auth-jwt-payload.decorator';
import { JwtAccessTokenOrUnrestrictedAuth } from '@/auth/jwt-access-token-or-unrestricted/jwt-or-unrestricted-auth.decorator';
import { JwtAccessTokenAuth } from '@/auth/jwt/jwt-access-token-auth.decorator';
import { JwtPayload } from '@/auth/jwt/types/jwt-payload.type';
import {
	EnvironmentVariables,
	JWT_ACCESS_TOKEN_COOKIE_NAME,
	JWT_REFRESH_TOKEN_COOKIE_NAME,
} from '@/config/config.constants';
import { Controller, Get, Query, Render, Res } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';
import { ApiOperation, ApiOkResponse, ApiTags } from '@nestjs/swagger';
import { Response } from 'express';

@ApiTags('Views')
@Controller('auth')
export class AuthViewsController {
	constructor(
		private readonly configService: ConfigService<EnvironmentVariables, true>,
	) {}

	@Get('login')
	@JwtAccessTokenOrUnrestrictedAuth()
	@ApiOperation({
		summary: 'Render the login page',
		description: 'Render the login page.',
		operationId: 'renderLogin',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	renderLogin(
		@AuthJwtPayload() payload: JwtPayload,
		@Res() res: Response,
	): void | Record<string, string> {
		if (payload?.sub) {
			return res.redirect('/');
		}

		return res.render('auth/login', {
			title: 'Log in | Spot in',
		});
	}

	@Get('logout')
	@JwtAccessTokenAuth()
	@ApiOperation({
		summary: 'Render the logout page',
		description: 'Render the logout page.',
		operationId: 'renderLogout',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@Render('auth/logout')
	renderLogout(@Res() res: Response): Record<string, string> {
		const accessTokenCookieName = this.configService.get(
			JWT_ACCESS_TOKEN_COOKIE_NAME,
			{ infer: true },
		);
		const refreshTokenCookieName = this.configService.get(
			JWT_REFRESH_TOKEN_COOKIE_NAME,
			{ infer: true },
		);

		res.clearCookie(accessTokenCookieName);
		res.clearCookie(refreshTokenCookieName);

		return {
			title: 'Logout | Spot in',
		};
	}

	@Get('register')
	@JwtAccessTokenOrUnrestrictedAuth()
	@ApiOperation({
		summary: 'Render the signup page',
		description: 'Render the signup page.',
		operationId: 'renderRegister',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	renderRegister(
		@AuthJwtPayload() payload: JwtPayload,
		@Res() res: Response,
	): void | Record<string, string> {
		if (payload?.sub) {
			return res.redirect('/');
		}

		return res.render('auth/register', {
			title: 'Register | Spot in',
		});
	}

	@Get('reset-password-request')
	@JwtAccessTokenOrUnrestrictedAuth()
	@ApiOperation({
		summary: 'Render the reset password request page',
		description: 'Render the reset password request page.',
		operationId: 'renderResetPasswordRequest',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	renderResetPasswordRequest(
		@AuthJwtPayload() payload: JwtPayload,
		@Res() res: Response,
	): void | Record<string, string> {
		if (payload?.sub) {
			return res.redirect('/');
		}

		return res.render('auth/reset-password-request', {
			title: 'Reset password request | Spot in',
		});
	}

	@Get('reset-password')
	@JwtAccessTokenOrUnrestrictedAuth()
	@ApiOperation({
		summary: 'Render the reset password page',
		description: 'Render the reset password page.',
		operationId: 'renderResetPassword',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	renderResetPassword(
		@AuthJwtPayload() payload: JwtPayload,
		@Res() res: Response,
		@Query('token') token: string,
	): void | Record<string, string> {
		if (payload?.sub) {
			return res.redirect('/');
		}

		if (!token) {
			return res.redirect('/auth/reset-password-request');
		}

		return res.render('auth/reset-password', {
			title: 'Reset password | Spot in',
			token,
		});
	}
}
