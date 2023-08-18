import * as qrcode from 'qrcode';
import {
  Get,
  Controller,
  Render,
  Param,
  Res,
  UseFilters,
  Session,
  Redirect,
  Post,
  Body,
  HttpCode,
} from '@nestjs/common';
import {
  ApiCreatedResponse,
  ApiNotFoundResponse,
  ApiOkResponse,
  ApiOperation,
  ApiParam,
  ApiTags,
} from '@nestjs/swagger';
import { Prisma, User } from '@prisma/client';
import { Response } from 'express';
import { ConfigService } from '@nestjs/config';
import { SpotsService } from '@/spots/spots.service';
import { JwtAuth } from '@/auth/jwt/jwt-auth.decorator';
import { FQDN } from '@/config/config.constants';
import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { ReadSpotDto } from '@/spots/dtos/read-spot.dto';
import { UnauthorizedMvcExceptionFilter } from '@/common/filters/unauthorized-mvc-exception.filter';
import { JwtOrUnrestrictedAuth } from '@/auth/jwt-or-unrestricted/jwt-or-unrestricted-auth.decorator';
import { BadRequestMvcExceptionFilter } from '@/common/filters/bad-request-mvc-exception.filter';
import { SessionData } from 'express-session';
import { UpdateSpotDto } from '@/spots/dtos/update-spot-type.dto';
import { CreateSpotDto } from '@/spots/dtos/create-spot.dto';
import { UnconfiguredSpotAuth } from '@/auth/unconfigured-spot/unconfigured-spot-auth.decorator';
import { ConfiguredSpotMvcExceptionFilter } from '@/spots/filters/configured-spot-mvc-exception.filter';
import { NotFoundMvcExceptionFilter } from '@/common/filters/not-found-mvc-exception.filter';

@ApiTags('MVC - Spots')
@Controller('spots')
@UseFilters(UnauthorizedMvcExceptionFilter)
@UseFilters(BadRequestMvcExceptionFilter)
@UseFilters(NotFoundMvcExceptionFilter)
export class SpotsMvcController {
  constructor(
    private readonly spotsService: SpotsService,
    private readonly configService: ConfigService,
  ) {}

  @Get('create')
  @JwtAuth()
  @ApiOperation({
    summary: 'Render the create a new spot page',
    description: 'Render the create a new spot page.',
    operationId: 'renderCreateSpotMvc',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('spots/form')
  renderCreateSpotMvc(@AuthUser() user: User, @Session() session: SessionData) {
    // Get errors and body from the session
    const { errors, body } = session;

    return {
      title: 'Create a new spot - Spot in',
      username: user?.username,
      email: user?.email,
      role: user?.role,
      errors,
      spot: body,
      action: `/spots`,
    };
  }

  @Get('latest')
  @JwtOrUnrestrictedAuth()
  @ApiOperation({
    summary: 'Render the list of latest public spots page',
    description: 'Render the list of latest public spots page.',
    operationId: 'renderLatestSpotsMvc',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('spots/latest')
  async renderLatestSpotsMvc(@AuthUser() user: User | undefined) {
    const spots = await this.spotsService.getPublicSpots();

    return {
      title: 'Latest spots - Spot in',
      username: user?.username,
      email: user?.email,
      role: user?.role,
      spots,
    };
  }

  @Get()
  @JwtAuth()
  @ApiOperation({
    summary: 'Render the spots page',
    description: 'Render the spots page.',
    operationId: 'renderSpotsListMvc',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('spots/list')
  async renderSpotsListMvc(@AuthUser() user: User) {
    const spots = await this.spotsService.getSpots(user);

    return {
      title: 'Spots - Spot in',
      username: user.username,
      email: user.email,
      role: user.role,
      spots,
    };
  }

  @Get(':id/configure')
  @UnconfiguredSpotAuth()
  @UseFilters(ConfiguredSpotMvcExceptionFilter)
  @ApiOperation({
    summary: 'Render the configure a spot page',
    description: 'Render the configure a spot page.',
    operationId: 'renderConfigureSpotMvc',
  })
  @ApiParam({
    name: 'id',
    description: 'The spot ID.',
    format: 'uuid',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('spots/form')
  async renderConfigureSpotMvc(
    @AuthUser() user: User,
    @Param('id') id: string,
    @Session() session: SessionData,
  ) {
    const spot = await this.spotsService.getSpot(id, user);

    // Get errors and body from the session
    const { errors, body } = session;

    return {
      username: user?.username,
      email: user?.email,
      role: user?.role,
      spot: body ? body : spot,
      errors,
      action: `/spots/${id}/configure`,
    };
  }

  @Post(':id/configure')
  @UnconfiguredSpotAuth()
  @UseFilters(ConfiguredSpotMvcExceptionFilter)
  @HttpCode(200)
  @ApiOperation({
    summary: 'Configure the spot',
    description: 'Configure the spot. Redirect to `/spots/:id`.',
    operationId: 'configureSpotMvc',
  })
  @ApiParam({
    name: 'id',
    description: 'The spot ID.',
    format: 'uuid',
  })
  @ApiOkResponse({
    description: 'Redirect successful.',
  })
  async configureSpotMvc(
    @AuthUser() user: User,
    @Param('id') id: string,
    @Body() updateSpot: UpdateSpotDto,
    @Res({ passthrough: true }) res: Response,
    @Session() session: SessionData,
  ) {
    updateSpot.configured = true;
    updateSpot.referenced = undefined;

    await this.spotsService.updateSpot(
      id,
      {
        ...updateSpot,
        // https://www.prisma.io/docs/concepts/components/prisma-client/working-with-fields/working-with-json-fields#using-null-values
        payload: updateSpot.payload ? updateSpot.payload : Prisma.DbNull,
      },
      user,
    );

    // Clear session
    delete session.errors;
    delete session.body;

    res.redirect(`/spots/${id}`);
  }

  @Get(':id/delete')
  @JwtAuth()
  @ApiOperation({
    summary: 'Delete the specified spot',
    description: 'Delete the specified spot. Redirect to `/spots` page.',
    operationId: 'deleteSpotMvc',
  })
  @ApiParam({
    name: 'id',
    description: 'The spot ID.',
    format: 'uuid',
  })
  @ApiOkResponse({
    description: 'Redirect successful.',
  })
  @Redirect('/spots')
  async deleteSpotMvc(@AuthUser() user: User, @Param('id') id: string) {
    await this.spotsService.deleteSpot(id, user);
  }

  @Get(':id/edit')
  @JwtAuth()
  @ApiOperation({
    summary: 'Render the edit a spot page',
    description: 'Render the edit a spot page.',
    operationId: 'renderEditSpotMvc',
  })
  @ApiParam({
    name: 'id',
    description: 'The spot ID.',
    format: 'uuid',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  async renderEditSpotMvc(
    @AuthUser() user: User,
    @Param('id') id: string,
    @Session() session: SessionData,
    @Res() res: Response,
  ) {
    try {
      const spot = await this.spotsService.getSpot(id, user);

      // Get errors and body from the session
      const { errors, body } = session;

      res.render('spots/form', {
        username: user?.username,
        email: user?.email,
        role: user?.role,
        spot: body ? body : spot,
        errors,
        action: `/spots/${id}`,
      });
    } catch (error) {
      res.redirect(`/spots/${id}/configure`);
    }
  }

  @Get(':id/redirect')
  @ApiOperation({
    summary: 'Redirect to the link specified by the spot',
    description: 'Redirect to the link specified by the spot.',
    operationId: 'getSpotRedirection',
  })
  @ApiParam({
    name: 'id',
    description: 'The spot ID.',
    format: 'uuid',
  })
  @ApiOkResponse({
    description: 'Redirect successful.',
    type: ReadSpotDto,
  })
  @ApiNotFoundResponse({
    description: 'Spot has not been found.',
  })
  async getSpotRedirection(
    @Res({ passthrough: true }) res: Response,
    @Param('id') id: string,
  ) {
    const spot = await this.spotsService.getSpot(id);

    if (!spot.configured) {
      res.redirect(`/spots/${id}/configure`);
    } else if (!spot.redirection) {
      res.redirect(`/spots/${id}`);
    } else {
      res.redirect(spot.redirection);
    }
  }

  @Get(':id')
  @JwtOrUnrestrictedAuth()
  @ApiOperation({
    summary: 'Render the specified spot page',
    description: 'Render the specified spot page.',
    operationId: 'renderSpotMvc',
  })
  @ApiParam({
    name: 'id',
    description: 'The spot ID.',
    format: 'uuid',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @ApiNotFoundResponse({
    description: 'Spot has not been found. Redirect to `/not-found` page.',
  })
  async renderSpotMvc(
    @AuthUser() user: User | undefined,
    @Res() res: Response,
    @Param('id') id: string,
  ) {
    const spot = await this.spotsService.getSpot(id);
    const fqdn = this.configService.get(FQDN, { infer: true });
    const redirection = `${fqdn}/spots/${spot.id}/redirect`;

    try {
      const qrcodeSvg = await qrcode.toString(redirection, { type: 'svg' });

      res.render('spots/view', {
        username: user?.username,
        email: user?.email,
        role: user?.role,
        title: 'Spot',
        spot,
        qrcode: qrcodeSvg,
      });
    } catch (error) {
      console.error(error);
    }
  }

  @Post(':id')
  @JwtAuth()
  @HttpCode(200)
  @ApiOperation({
    summary: 'Update the specified spot',
    description: 'Update the specified spot. Redirect to `/spots/:id`.',
    operationId: 'updateSpotMvc',
  })
  @ApiParam({
    name: 'id',
    description: 'The spot ID.',
    format: 'uuid',
  })
  @ApiOkResponse({
    description: 'Redirect successful.',
  })
  async updateSpotMvc(
    @AuthUser() user: User,
    @Param('id') id: string,
    @Body() updateSpot: UpdateSpotDto,
    @Res({ passthrough: true }) res: Response,
    @Session() session: SessionData,
  ) {
    await this.spotsService.updateSpot(
      id,
      {
        ...updateSpot,
        // https://www.prisma.io/docs/concepts/components/prisma-client/working-with-fields/working-with-json-fields#using-null-values
        payload: updateSpot.payload ? updateSpot.payload : Prisma.DbNull,
      },
      user,
    );

    // Clear session
    delete session.errors;
    delete session.body;

    res.redirect(`/spots/${id}`);
  }

  @Post()
  @JwtAuth()
  @ApiOperation({
    summary: 'Create a new spot',
    description: 'Create a new spot. Redirect to `/spots/:id`.',
    operationId: 'createSpotMvc',
  })
  @ApiCreatedResponse({
    description: 'Redirect successful.',
  })
  async createSpotMvc(
    @AuthUser() user: User,
    @Body() createSpotDto: CreateSpotDto,
    @Res({ passthrough: true }) res: Response,
    @Session() session: SessionData,
  ) {
    const newSpot = await this.spotsService.createSpot(
      createSpotDto as Prisma.SpotCreateWithoutUsersInput,
      user,
    );

    // Clear session
    delete session.errors;
    delete session.body;

    res.redirect(`/spots/${newSpot.id}`);
  }
}
