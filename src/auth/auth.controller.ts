import {
	Controller,
	Post,
	Body,
	HttpCode,
	ConflictException,
	Req,
	Res,
} from '@nestjs/common';
import {
	ApiBody,
	ApiConflictResponse,
	ApiNoContentResponse,
	ApiOperation,
	ApiTags,
} from '@nestjs/swagger';
import { LoginUserDto } from '@/auth/email-password/dtos/login-user.dto';
import { UsersService } from '@/users/users.service';
import { EmailPasswordAuth } from '@/auth/email-password/email-password-auth.decorator';
import { RegisterUserDto } from '@/auth/email-password/dtos/register-user.dto';
import { ResetPasswordAuth } from '@/auth/reset-password/reset-password.decorator';
import { ResetPasswordDto } from '@/auth/reset-password/dtos/reset-password.dto';
import { ResetPasswordRequestsService } from '@/reset-password-requests/reset-password-requests.service';
import { ResetPasswordRequestDto } from '@/auth/reset-password/dtos/reset-password-request.dto';
import { PrismaClientKnownRequestError } from '@prisma/client/runtime/library';
import { UserRole } from '@/users/enums/user-role';
import { I18n, I18nContext } from 'nestjs-i18n';
import {
	EnvironmentVariables,
	SESSION_COOKIE_NAME,
} from '@/config/config.constants';
import { ConfigService } from '@nestjs/config';
import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { User } from '@/users/types/user';
import { Request, Response } from 'express';
import { SessionAuth } from '@/auth/session/session-auth.decorator';

@ApiTags('Auth')
@Controller('api/auth')
export class AuthController {
	constructor(
		private readonly configService: ConfigService<EnvironmentVariables, true>,
		private readonly usersService: UsersService,
		private readonly resetPasswordRequestsService: ResetPasswordRequestsService,
	) {}

	@Post('login')
	@EmailPasswordAuth()
	@HttpCode(204)
	@ApiOperation({
		summary: 'Log in with email and password',
		description: 'Log in with email and password.',
		operationId: 'login',
	})
	@ApiBody({
		description: "The user's credentials.",
		type: LoginUserDto,
	})
	@ApiNoContentResponse({
		description: 'The user has been successfully logged in.',
		headers: {
			'Set-Cookie': {
				description: 'The session cookie for the user.',
				schema: {
					type: 'string',
				},
			},
		},
	})
	async login(@AuthUser() user: User, @Req() req: Request): Promise<void> {
		await this.resetPasswordRequestsService.deleteResetPasswordRequestForUser(
			user,
		);

		return new Promise((resolve) => req.logIn(user, () => resolve()));
	}

	@Post('logout')
	@HttpCode(204)
	@SessionAuth()
	@ApiOperation({
		summary: 'Log out the user',
		description: 'Log out the user.',
		operationId: 'logout',
	})
	@ApiNoContentResponse({
		description: 'The user has been successfully logged out.',
		headers: {
			'Set-Cookie': {
				description: 'Clear the session cookie for the user.',
				schema: {
					type: 'string',
				},
			},
		},
	})
	async logout(
		@Req() req: Request,
		@Res({ passthrough: true }) res: Response,
	): Promise<void> {
		const sessionName = this.configService.get(SESSION_COOKIE_NAME, {
			infer: true,
		});

		res.clearCookie(sessionName);

		await Promise.all([
			new Promise<void>((resolve) => req.session.destroy(() => resolve())),
			new Promise<void>((resolve) => req.logOut(() => resolve())),
		]);
	}

	@Post('register')
	@HttpCode(204)
	@ApiOperation({
		summary: 'Register a new user',
		description: 'Register a new user.',
		operationId: 'register',
	})
	@ApiBody({
		description: "The user's details.",
		type: RegisterUserDto,
	})
	@ApiNoContentResponse({
		description: 'The user has been successfully signed up.',
	})
	@ApiConflictResponse({
		description: 'The username is already taken.',
	})
	async register(
		@Body() registerUserDto: RegisterUserDto,
		@I18n() i18n: I18nContext,
	): Promise<void> {
		try {
			await this.usersService.createUser(
				{
					...registerUserDto,
					enabled: true,
					role: UserRole.STANDARD_USER,
				},
				i18n.lang,
			);
		} catch (e) {
			if (e instanceof PrismaClientKnownRequestError && e.code === 'P2002') {
				const meta = e.meta as Record<string, Array<string>>;

				if (meta.target.includes('username')) {
					throw new ConflictException();
				}

				try {
					const user = await this.usersService.getUserByEmail(
						registerUserDto.email,
					);

					if (user.enabled) {
						await this.resetPasswordRequestsService.sendRecoverAccountForUser(
							user,
							i18n.lang,
						);
					}
				} catch {
					// Do nothing
				}
			} else {
				throw e;
			}
		}
	}

	@Post('reset-password-request')
	@HttpCode(204)
	@ApiOperation({
		summary: 'Request password reset for user',
		description: 'Request password reset for user.',
		operationId: 'resetPasswordRequest',
	})
	@ApiBody({
		description: 'The email address of the user.',
		type: ResetPasswordRequestDto,
	})
	@ApiNoContentResponse({
		description:
			'The user password reset has been successfully requested. Note: if the email is inexistent, nothing will happen.',
	})
	async resetPasswordRequest(
		@Body() requestPasswordResetDto: ResetPasswordRequestDto,
		@I18n() i18n: I18nContext,
	): Promise<void> {
		try {
			const user = await this.usersService.getUserByEmail(
				requestPasswordResetDto.email,
			);

			if (user.enabled) {
				await this.resetPasswordRequestsService.sendResetPasswordRequestForUser(
					user,
					i18n.lang,
				);
			}
		} catch {
			// Do nothing
		}
	}

	@Post('reset-password')
	@ResetPasswordAuth()
	@HttpCode(204)
	@ApiOperation({
		summary: 'Reset password for user',
		description: 'Reset password for user.',
		operationId: 'resetPassword',
	})
	@ApiBody({
		description: 'The new password.',
		type: ResetPasswordDto,
	})
	@ApiNoContentResponse({
		description: 'The user password has been successfully reset.',
	})
	async resetPassword(
		@AuthUser() user: User,
		@Body() resetPasswordDto: ResetPasswordDto,
	): Promise<void> {
		await this.resetPasswordRequestsService.deleteResetPasswordRequestForUser(
			user,
		);

		await this.usersService.updateUser(user.id, {
			password: resetPasswordDto.password,
		});
	}
}
